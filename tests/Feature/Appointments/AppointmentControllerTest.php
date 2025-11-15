<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\OfertaComponente;
use App\Models\RecursoDidatico;
use App\Models\Agendamento;
use App\Models\Municipio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ModelActionNotification;
use PHPUnit\Framework\Attributes\Test;

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretorA;
    private Usuario $professorA;
    private Usuario $diretorB;
    private Escola $escolaA;
    private Escola $escolaB;
    private OfertaComponente $ofertaA;
    private OfertaComponente $ofertaB;
    private RecursoDidatico $recursoA;
    private RecursoDidatico $recursoB;
    private Agendamento $agendamentoA;
    private Agendamento $agendamentoB;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();

        $municipioA = Municipio::factory()->create();
        $this->escolaA = new Escola([
            'nome' => 'Escola Teste A',
            'nivel_ensino' => 'Médio',
            'tipo' => 'Estadual',
            'id_municipio' => $municipioA->id_municipio,
        ]);
        $this->escolaA->save();

        $municipioB = Municipio::factory()->create();
        $this->escolaB = new Escola([
            'nome' => 'Escola Teste B',
            'nivel_ensino' => 'Médio',
            'tipo' => 'Estadual',
            'id_municipio' => $municipioB->id_municipio,
        ]);
        $this->escolaB->save();

        $this->admin = Usuario::factory()->create([
            'tipo_usuario' => 'Administrador',
            'id_escola' => null,
        ]);
        $this->diretorA = Usuario::factory()->create([
            'tipo_usuario' => 'Diretor',
            'id_escola' => $this->escolaA->id_escola,
        ]);
        $this->professorA = Usuario::factory()->create([
            'tipo_usuario' => 'Professor',
            'id_escola' => $this->escolaA->id_escola,
        ]);
        $this->diretorB = Usuario::factory()->create([
            'tipo_usuario' => 'Diretor',
            'id_escola' => $this->escolaB->id_escola,
        ]);
        
        $professorB = Usuario::factory()->create([
            'tipo_usuario' => 'Professor',
            'id_escola' => $this->escolaB->id_escola,
        ]);

        $componenteA = ComponenteCurricular::factory()->create([
            'id_escola' => $this->escolaA->id_escola,
            'status' => 'aprovado'
        ]);
        $turmaA = Turma::factory()->create(['id_escola' => $this->escolaA->id_escola]);
        
        $this->ofertaA = new OfertaComponente([
            'id_turma' => $turmaA->id_turma,
            'id_componente' => $componenteA->id_componente_curricular,
            'id_professor' => $this->professorA->id_usuario,
        ]);
        $this->ofertaA->save();
        
        $componenteB = ComponenteCurricular::factory()->create([
            'id_escola' => $this->escolaB->id_escola,
            'status' => 'aprovado'
        ]);
        $turmaB = Turma::factory()->create(['id_escola' => $this->escolaB->id_escola]);

        $this->ofertaB = new OfertaComponente([
            'id_turma' => $turmaB->id_turma,
            'id_componente' => $componenteB->id_componente_curricular,
            'id_professor' => $professorB->id_usuario,
        ]);
        $this->ofertaB->save();
        
        $this->recursoA = RecursoDidatico::factory()->create(['id_escola' => $this->escolaA->id_escola, 'status' => 'funcionando']);
        $this->recursoB = RecursoDidatico::factory()->create(['id_escola' => $this->escolaB->id_escola, 'status' => 'funcionando']);

        $this->agendamentoA = new Agendamento([
            'id_recurso' => $this->recursoA->id_recurso,
            'id_oferta' => $this->ofertaA->id_oferta,
            'data_hora_inicio' => now()->addDay()->startOfHour(),
            'data_hora_fim' => now()->addDay()->addHour(),
            'status' => 'aprovado',
        ]);
        $this->agendamentoA->save();

        $this->agendamentoB = new Agendamento([
            'id_recurso' => $this->recursoB->id_recurso,
            'id_oferta' => $this->ofertaB->id_oferta,
            'data_hora_inicio' => now()->addDay()->startOfHour(),
            'data_hora_fim' => now()->addDay()->addHour(),
            'status' => 'aprovado',
        ]);
        $this->agendamentoB->save();
    }

    #[Test]
    public function admin_pode_visualizar_todos_agendamentos_na_index()
    {
        $response = $this->actingAs($this->admin)->get(route('agendamentos.index'));
        $response->assertOk();
        $response->assertViewHas('meusAgendamentos', function ($agendamentos) {
            return $agendamentos->count() === 2;
        });
    }

    #[Test]
    public function diretor_pode_visualizar_agendamentos_da_propria_escola_na_index()
    {
        $response = $this->actingAs($this->diretorA)->get(route('agendamentos.index'));
        $response->assertOk();
        $response->assertViewHas('meusAgendamentos', function ($agendamentos) {
            return $agendamentos->count() === 1 && $agendamentos->first()->id_agendamento === $this->agendamentoA->id_agendamento;
        });
    }

    #[Test]
    public function professor_pode_visualizar_proprios_agendamentos_na_index()
    {
        $response = $this->actingAs($this->professorA)->get(route('agendamentos.index'));
        $response->assertOk();
        $response->assertViewHas('meusAgendamentos', function ($agendamentos) {
            return $agendamentos->count() === 1 && $agendamentos->first()->id_agendamento === $this->agendamentoA->id_agendamento;
        });
    }

    #[Test]
    public function admin_pode_obter_todos_eventos_do_calendario()
    {
        $response = $this->actingAs($this->admin)->get(route('agendamentos.events', ['start' => now()->subMonth()->toIso8601String(), 'end' => now()->addMonth()->toIso8601String()]));
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    #[Test]
    public function diretor_pode_obter_eventos_do_calendario_da_propria_escola()
    {
        $response = $this->actingAs($this->diretorA)->get(route('agendamentos.events', ['start' => now()->subMonth()->toIso8601String(), 'end' => now()->addMonth()->toIso8601String()]));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id_agendamento' => $this->agendamentoA->id_agendamento]);
    }

    #[Test]
    public function admin_pode_obter_disponibilidade_para_todas_escolas()
    {
        $response = $this->actingAs($this->admin)->post(route('agendamentos.availability'), ['date' => now()->addDay()->format('Y-m-d')]);
        $response->assertOk();
        $response->assertJsonPath('agendados.total', 2);
        $response->assertJsonPath('disponiveis.total', 2);
    }

    #[Test]
    public function diretor_pode_obter_disponibilidade_para_propria_escola()
    {
        RecursoDidatico::factory()->create(['id_escola' => null, 'status' => 'funcionando']);
        $response = $this->actingAs($this->diretorA)->post(route('agendamentos.availability'), ['date' => now()->addDay()->format('Y-m-d')]);
        $response->assertOk();
        $response->assertJsonPath('agendados.total', 1);
        $response->assertJsonPath('disponiveis.total', 2);
    }

    #[Test]
    public function store_cria_agendamento_e_envia_notificacoes()
    {
        $data = [
            'id_recurso' => $this->recursoA->id_recurso,
            'id_oferta' => $this->ofertaA->id_oferta,
            'data_hora_inicio' => now()->addHours(2)->toDateTimeString(),
            'data_hora_fim' => now()->addHours(3)->toDateTimeString(),
        ];

        $response = $this->actingAs($this->professorA)->post(route('agendamentos.store'), $data);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('agendamentos', ['id_recurso' => $this->recursoA->id_recurso, 'id_oferta' => $this->ofertaA->id_oferta]);
        Notification::assertSentTo($this->admin, ModelActionNotification::class);
        Notification::assertSentTo($this->diretorA, ModelActionNotification::class);
        Notification::assertSentTo($this->professorA, ModelActionNotification::class);
    }

    #[Test]
    public function store_falha_por_conflito_de_horario()
    {
        $data = [
            'id_recurso' => $this->agendamentoA->id_recurso,
            'id_oferta' => $this->agendamentoA->id_oferta,
            'data_hora_inicio' => $this->agendamentoA->data_hora_inicio,
            'data_hora_fim' => $this->agendamentoA->data_hora_fim,
        ];

        $response = $this->actingAs($this->professorA)->post(route('agendamentos.store'), $data);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('data_hora_inicio');
    }

    #[Test]
    public function store_falha_por_horario_nao_permitido()
    {
        $data = [
            'id_recurso' => $this->recursoA->id_recurso,
            'id_oferta' => $this->ofertaA->id_oferta,
            'data_hora_inicio' => now()->addDay()->setTime(23, 30, 0)->toDateTimeString(),
            'data_hora_fim' => now()->addDay()->setTime(23, 59, 0)->toDateTimeString(),
        ];

        $response = $this->actingAs($this->professorA)->post(route('agendamentos.store'), $data);
        
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Não é permitido criar agendamentos entre 23:00 e 06:00.']);
    }

    #[Test]
    public function store_falha_pela_politica_de_professor()
    {
        $data = [
            'id_recurso' => $this->recursoA->id_recurso,
            'id_oferta' => $this->ofertaA->id_oferta,
            'data_hora_inicio' => now()->addHours(2)->toDateTimeString(),
            'data_hora_fim' => now()->addHours(3)->toDateTimeString(),
        ];
        
        $outroProfessor = Usuario::factory()->create([
            'tipo_usuario' => 'Professor',
            'id_escola' => $this->escolaA->id_escola,
        ]);

        $response = $this->actingAs($outroProfessor)->post(route('agendamentos.store'), $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('id_oferta');
    }

    #[Test]
    public function destroy_deleta_agendamento_e_envia_notificacoes()
    {
        $response = $this->actingAs($this->admin)->delete(route('agendamentos.destroy', $this->agendamentoA));
        
        $response->assertOk();
        $response->assertJson(['message' => 'Agendamento cancelado com sucesso.']);
        $this->assertDatabaseMissing('agendamentos', ['id_agendamento' => $this->agendamentoA->id_agendamento]);
        
        Notification::assertSentTo($this->admin, ModelActionNotification::class);
        Notification::assertSentTo($this->diretorA, ModelActionNotification::class);
        Notification::assertSentTo($this->professorA, ModelActionNotification::class);
    }

    #[Test]
    public function destroy_falha_pela_regra_de_10_minutos()
    {
        $agendamento = new Agendamento([
            'id_recurso' => $this->recursoA->id_recurso,
            'id_oferta' => $this->ofertaA->id_oferta,
            'data_hora_inicio' => now()->addMinutes(5),
            'data_hora_fim' => now()->addMinutes(65),
            'status' => 'aprovado',
        ]);
        $agendamento->save();
        
        $response = $this->actingAs($this->professorA)->delete(route('agendamentos.destroy', $agendamento));
        
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Agendamentos não podem ser cancelados com menos de 10 minutos de antecedência do seu início.']);
    }

    #[Test]
    public function destroy_falha_por_politica_de_acesso()
    {
        $outroProfessor = Usuario::factory()->create([
            'tipo_usuario' => 'Professor',
            'id_escola' => $this->escolaA->id_escola,
        ]);
        $response = $this->actingAs($outroProfessor)->delete(route('agendamentos.destroy', $this->agendamentoA));
        
        $response->assertForbidden();
    }
}