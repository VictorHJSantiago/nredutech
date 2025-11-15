<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\OfertaComponente;
use App\Models\Agendamento;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\RecursoDidatico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AppointmentRoutesTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;
    private Usuario $outroProfessor;
    private Agendamento $agendamentoProfessor;
    private Agendamento $agendamentoOutroProfessor;
    private RecursoDidatico $recurso;
    private OfertaComponente $ofertaProfessor;

    protected function setUp(): void
    {
        parent::setUp();

        $municipio = Municipio::factory()->create();
        $escola = new Escola([
            'nome' => 'Escola Teste Rota',
            'nivel_ensino' => 'Médio',
            'tipo' => 'Estadual',
            'id_municipio' => $municipio->id_municipio,
        ]);
        $escola->save();
        
        $this->admin = Usuario::factory()->create([
            'tipo_usuario' => 'Administrador',
            'id_escola' => null,
        ]);
        $this->diretor = Usuario::factory()->create([
            'tipo_usuario' => 'Diretor',
            'id_escola' => $escola->id_escola,
        ]);
        $this->professor = Usuario::factory()->create([
            'tipo_usuario' => 'Professor',
            'id_escola' => $escola->id_escola,
        ]);
        $this->outroProfessor = Usuario::factory()->create([
            'tipo_usuario' => 'Professor',
            'id_escola' => $escola->id_escola,
        ]);

        $this->recurso = RecursoDidatico::factory()->create(['id_escola' => $escola->id_escola, 'status' => 'funcionando']);

        $turmaA = Turma::factory()->create(['id_escola' => $escola->id_escola]);
        $componenteA = ComponenteCurricular::factory()->create([
            'id_escola' => $escola->id_escola,
            'status' => 'aprovado'
        ]);
        $this->ofertaProfessor = new OfertaComponente([
            'id_turma' => $turmaA->id_turma,
            'id_componente' => $componenteA->id_componente_curricular,
            'id_professor' => $this->professor->id_usuario
        ]);
        $this->ofertaProfessor->save();

        $turmaB = Turma::factory()->create(['id_escola' => $escola->id_escola]);
        $componenteB = ComponenteCurricular::factory()->create([
            'id_escola' => $escola->id_escola,
            'status' => 'aprovado'
        ]);
        $ofertaOutroProfessor = new OfertaComponente([
            'id_turma' => $turmaB->id_turma,
            'id_componente' => $componenteB->id_componente_curricular,
            'id_professor' => $this->outroProfessor->id_usuario
        ]);
        $ofertaOutroProfessor->save();

        $this->agendamentoProfessor = new Agendamento([
            'id_oferta' => $this->ofertaProfessor->id_oferta,
            'id_recurso' => $this->recurso->id_recurso,
            'data_hora_inicio' => now()->addDay(),
            'data_hora_fim' => now()->addDay()->addHour(),
            'status' => 'aprovado',
        ]);
        $this->agendamentoProfessor->save();

        $this->agendamentoOutroProfessor = new Agendamento([
            'id_oferta' => $ofertaOutroProfessor->id_oferta,
            'id_recurso' => $this->recurso->id_recurso,
            'data_hora_inicio' => now()->addDay()->addMinutes(10),
            'data_hora_fim' => now()->addDay()->addHour()->addMinutes(10),
            'status' => 'aprovado',
        ]);
        $this->agendamentoOutroProfessor->save();
    }

    #[Test]
    public function visitante_e_redirecionado_de_todas_rotas_de_agendamento()
    {
        $this->get(route('agendamentos.index'))->assertRedirect(route('login'));
        $this->get(route('agendamentos.events'))->assertRedirect(route('login'));
        $this->post(route('agendamentos.availability'))->assertRedirect(route('login'));
        $this->post(route('agendamentos.store'))->assertRedirect(route('login'));
        $this->delete(route('agendamentos.destroy', $this->agendamentoProfessor))->assertRedirect(route('login'));
    }

    #[Test]
    public function admin_pode_acessar_todas_rotas_de_agendamento()
    {
        $this->actingAs($this->admin);

        $this->get(route('agendamentos.index'))->assertOk();
        $this->get(route('agendamentos.events', ['start' => '2025-01-01', 'end' => '2025-01-31']))->assertOk();
        $this->post(route('agendamentos.availability'), ['date' => '2025-01-01'])->assertOk();
        
        $storeData = [
            'id_oferta' => $this->ofertaProfessor->id_oferta,
            'id_recurso' => $this->recurso->id_recurso,
            'data_hora_inicio' => now()->addDays(5)->toDateTimeString(),
            'data_hora_fim' => now()->addDays(5)->addHour()->toDateTimeString(),
        ];
        $this->post(route('agendamentos.store'), $storeData)->assertStatus(201);
        
        $this->delete(route('agendamentos.destroy', $this->agendamentoProfessor))->assertOk();
    }

    #[Test]
    public function diretor_pode_acessar_todas_rotas_de_agendamento()
    {
        $this->actingAs($this->diretor);

        $this->get(route('agendamentos.index'))->assertOk();
        $this->get(route('agendamentos.events', ['start' => '2025-01-01', 'end' => '2025-01-31']))->assertOk();
        $this->post(route('agendamentos.availability'), ['date' => '2025-01-01'])->assertOk();
        
        $storeData = [
            'id_oferta' => $this->ofertaProfessor->id_oferta,
            'id_recurso' => $this->recurso->id_recurso,
            'data_hora_inicio' => now()->addDays(5)->toDateTimeString(),
            'data_hora_fim' => now()->addDays(5)->addHour()->toDateTimeString(),
        ];
        $this->post(route('agendamentos.store'), $storeData)->assertStatus(201);
        
        $this->delete(route('agendamentos.destroy', $this->agendamentoProfessor))->assertOk();
    }

    #[Test]
    public function professor_pode_acessar_rotas_e_criar()
    {
        $this->actingAs($this->professor);

        $this->get(route('agendamentos.index'))->assertOk();
        $this->get(route('agendamentos.events', ['start' => '2025-01-01', 'end' => '2025-01-31']))->assertOk();
        $this->post(route('agendamentos.availability'), ['date' => '2025-01-01'])->assertOk();
        
        $storeData = [
            'id_oferta' => $this->ofertaProfessor->id_oferta,
            'id_recurso' => $this->recurso->id_recurso,
            'data_hora_inicio' => now()->addDays(5)->toDateTimeString(),
            'data_hora_fim' => now()->addDays(5)->addHour()->toDateTimeString(),
        ];
        $this->post(route('agendamentos.store'), $storeData)->assertStatus(201);
    }

    #[Test]
    public function professor_pode_destruir_proprio_agendamento()
    {
        $this->actingAs($this->professor)
             ->delete(route('agendamentos.destroy', $this->agendamentoProfessor))
             ->assertOk();
    }

    #[Test]
    public function professor_e_proibido_de_destruir_agendamento_de_outro()
    {
        $this->actingAs($this->professor)
             ->delete(route('agendamentos.destroy', $this->agendamentoOutroProfessor))
             ->assertForbidden();
    }
}