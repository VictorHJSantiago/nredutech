<?php

namespace Tests\Feature\Appointments;

use App\Models\Agendamento;
use App\Models\ComponenteCurricular;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\OfertaComponente;
use App\Models\RecursoDidatico;
use App\Models\Turma;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    private $escola;
    private $turma;
    private $professor;
    private $diretor;
    private $admin;
    private $componente;
    private $oferta;
    private $recurso;
    private $municipio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->municipio = Municipio::create(['nome' => 'Curitiba']);

        $this->escola = Escola::create([
            'nome' => 'Escola Teste',
            'nivel_ensino' => 'Médio',
            'tipo' => 'Estadual',
            'id_municipio' => $this->municipio->id_municipio
        ]);

        $this->admin = Usuario::factory()->create([
            'tipo_usuario' => 'administrador',
            'status_aprovacao' => 'ativo'
        ]);

        $this->diretor = Usuario::factory()->create([
            'tipo_usuario' => 'diretor',
            'id_escola' => $this->escola->id_escola,
            'status_aprovacao' => 'ativo'
        ]);

        $this->professor = Usuario::factory()->create([
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escola->id_escola,
            'status_aprovacao' => 'ativo'
        ]);

        $this->turma = Turma::create([
            'serie' => '1º Ano',
            'ano_letivo' => 2025,
            'turno' => 'manha',
            'nivel_escolaridade' => 'medio',
            'id_escola' => $this->escola->id_escola
        ]);

        $this->componente = ComponenteCurricular::create([
            'nome' => 'Matemática',
            'carga_horaria' => 60,
            'status' => 'aprovado'
        ]);

        $this->oferta = OfertaComponente::create([
            'id_turma' => $this->turma->id_turma,
            'id_professor' => $this->professor->id_usuario,
            'id_componente' => $this->componente->id_componente
        ]);

        $this->recurso = RecursoDidatico::create([
            'nome' => 'Projetor',
            'quantidade' => 1,
            'status' => 'funcionando',
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $this->admin->id_usuario
        ]);
    }

    public function test_admin_pode_visualizar_todos_agendamentos_na_index()
    {
        Agendamento::create([
            'data_hora_inicio' => now()->addDay()->setHour(10),
            'data_hora_fim' => now()->addDay()->setHour(11),
            'status' => 'agendado',
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ]);

        $response = $this->actingAs($this->admin)->get(route('agendamentos.index'));

        $response->assertStatus(200);
        $response->assertSee('Projetor');
    }

    public function test_diretor_pode_visualizar_agendamentos_da_propria_escola_na_index()
    {
        Agendamento::create([
            'data_hora_inicio' => now()->addDay()->setHour(10),
            'data_hora_fim' => now()->addDay()->setHour(11),
            'status' => 'agendado',
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ]);

        $outraEscola = Escola::create([
            'nome' => 'Outra Escola',
            'nivel_ensino' => 'Fundamental',
            'tipo' => 'Municipal',
            'id_municipio' => $this->municipio->id_municipio
        ]);
        
        $outraTurma = Turma::create([
            'serie' => '5º Ano',
            'ano_letivo' => 2025,
            'turno' => 'tarde',
            'nivel_escolaridade' => 'fundamental_1',
            'id_escola' => $outraEscola->id_escola
        ]);

        $outroProf = Usuario::factory()->create([
            'tipo_usuario' => 'professor',
            'id_escola' => $outraEscola->id_escola
        ]);

        $outraOferta = OfertaComponente::create([
            'id_turma' => $outraTurma->id_turma,
            'id_professor' => $outroProf->id_usuario,
            'id_componente' => $this->componente->id_componente
        ]);

        $outroRecurso = RecursoDidatico::create([
            'nome' => 'Outro Recurso',
            'quantidade' => 1,
            'status' => 'funcionando',
            'id_escola' => $outraEscola->id_escola,
            'id_usuario_criador' => $this->admin->id_usuario
        ]);

        Agendamento::create([
            'data_hora_inicio' => now()->addDay()->setHour(14),
            'data_hora_fim' => now()->addDay()->setHour(15),
            'status' => 'agendado',
            'id_oferta' => $outraOferta->id_oferta,
            'id_recurso' => $outroRecurso->id_recurso
        ]);

        $response = $this->actingAs($this->diretor)->get(route('agendamentos.index'));

        $response->assertStatus(200);
        $response->assertSee('Projetor');
        $response->assertDontSee('Outro Recurso');
    }

    public function test_professor_pode_visualizar_proprios_agendamentos_na_index()
    {
        Agendamento::create([
            'data_hora_inicio' => now()->addDay()->setHour(10),
            'data_hora_fim' => now()->addDay()->setHour(11),
            'status' => 'agendado',
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ]);

        $outroProf = Usuario::factory()->create([
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escola->id_escola
        ]);

        $outraOferta = OfertaComponente::create([
            'id_turma' => $this->turma->id_turma,
            'id_professor' => $outroProf->id_usuario,
            'id_componente' => $this->componente->id_componente
        ]);
        
        Agendamento::create([
            'data_hora_inicio' => now()->addDay()->setHour(14),
            'data_hora_fim' => now()->addDay()->setHour(15),
            'status' => 'agendado',
            'id_oferta' => $outraOferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ]);

        $response = $this->actingAs($this->professor)->get(route('agendamentos.index'));

        $response->assertStatus(200);
        $response->assertSee('Projetor');
        $this->assertEquals(1, $response->viewData('meusAgendamentos')->count());
    }

    public function test_admin_pode_obter_todos_eventos_do_calendario()
    {
        $inicioEvento = now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
        Agendamento::create([
            'data_hora_inicio' => $inicioEvento,
            'data_hora_fim' => $inicioEvento->copy()->addHour(),
            'status' => 'agendado',
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('appointments.events', [
            'start' => $inicioEvento->copy()->startOfDay()->toDateTimeString(),
            'end' => $inicioEvento->copy()->endOfDay()->toDateTimeString()
        ]));

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_diretor_pode_obter_eventos_do_calendario_da_propria_escola()
    {
        $inicioEvento = now()->addDay()->setHour(10)->setMinute(30)->setSecond(0);
        Agendamento::create([
            'data_hora_inicio' => $inicioEvento,
            'data_hora_fim' => $inicioEvento->copy()->addHour(),
            'status' => 'agendado',
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ]);

        $response = $this->actingAs($this->diretor)->getJson(route('appointments.events', [
            'start' => $inicioEvento->copy()->startOfDay()->toDateTimeString(),
            'end' => $inicioEvento->copy()->endOfDay()->toDateTimeString()
        ]));

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_admin_pode_obter_disponibilidade_para_todas_escolas()
    {
        $response = $this->actingAs($this->admin)->postJson(route('appointments.availability'), [
            'date' => now()->addDay()->toDateString()
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['disponiveis', 'agendados']);
    }

    public function test_diretor_pode_obter_disponibilidade_para_propria_escola()
    {
        $response = $this->actingAs($this->diretor)->postJson(route('appointments.availability'), [
            'date' => now()->addDay()->toDateString()
        ]);

        $response->assertStatus(200);
    }

    public function test_store_cria_agendamento_e_envia_notificacoes()
    {
        $dados = [
            'data_hora_inicio' => now()->addWeek()->setHour(10)->minute(0)->format('Y-m-d H:i:s'),
            'data_hora_fim' => now()->addWeek()->setHour(11)->minute(0)->format('Y-m-d H:i:s'),
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ];

        $response = $this->actingAs($this->professor)->postJson(route('agendamentos.store'), $dados);

        $response->assertStatus(201);
        $this->assertDatabaseHas('agendamentos', [
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso,
            'status' => 'agendado'
        ]);
    }

    public function test_store_falha_por_conflito_de_horario()
    {
        $inicio = now()->addWeek()->setHour(10)->minute(0);
        $fim = now()->addWeek()->setHour(11)->minute(0);

        Agendamento::create([
            'data_hora_inicio' => $inicio,
            'data_hora_fim' => $fim,
            'status' => 'agendado',
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ]);

        $dados = [
            'data_hora_inicio' => $inicio->format('Y-m-d H:i:s'),
            'data_hora_fim' => $fim->format('Y-m-d H:i:s'),
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ];

        $response = $this->actingAs($this->professor)->postJson(route('agendamentos.store'), $dados);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Este recurso já está agendado para o período selecionado.']);
    }

    public function test_store_falha_por_horario_nao_permitido()
    {
        $dados = [
            'data_hora_inicio' => now()->addWeek()->setHour(23)->minute(30)->format('Y-m-d H:i:s'),
            'data_hora_fim' => now()->addWeek()->addDay()->setHour(0)->minute(30)->format('Y-m-d H:i:s'),
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ];

        $response = $this->actingAs($this->professor)->postJson(route('agendamentos.store'), $dados);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Não é permitido criar agendamentos entre 23:00 e 06:00.']);
    }

    public function test_store_falha_pela_politica_de_professor()
    {
        $outroProf = Usuario::factory()->create([
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escola->id_escola
        ]);

        $dados = [
            'data_hora_inicio' => now()->addWeek()->setHour(10)->format('Y-m-d H:i:s'),
            'data_hora_fim' => now()->addWeek()->setHour(11)->format('Y-m-d H:i:s'),
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ];

        $response = $this->actingAs($outroProf)->postJson(route('agendamentos.store'), $dados);

        $response->assertStatus(403);
    }

    public function test_destroy_deleta_agendamento_e_envia_notificacoes()
    {
        $agendamento = Agendamento::create([
            'data_hora_inicio' => now()->addDays(2)->setHour(10),
            'data_hora_fim' => now()->addDays(2)->setHour(11),
            'status' => 'agendado',
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ]);

        $response = $this->actingAs($this->professor)->deleteJson(route('agendamentos.destroy', $agendamento->id_agendamento));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('agendamentos', ['id_agendamento' => $agendamento->id_agendamento]);
    }

    public function test_destroy_falha_pela_regra_de_10_minutos()
    {
        $agendamento = Agendamento::create([
            'data_hora_inicio' => now()->addMinutes(5),
            'data_hora_fim' => now()->addMinutes(65),
            'status' => 'agendado',
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ]);

        $response = $this->actingAs($this->professor)->deleteJson(route('agendamentos.destroy', $agendamento->id_agendamento));

        $response->assertStatus(422);
    }

    public function test_destroy_falha_por_politica_de_acesso()
    {
        $agendamento = Agendamento::create([
            'data_hora_inicio' => now()->addDays(2)->setHour(10),
            'data_hora_fim' => now()->addDays(2)->setHour(11),
            'status' => 'agendado',
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ]);

        $outroProf = Usuario::factory()->create([
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escola->id_escola
        ]);

        $response = $this->actingAs($outroProf)->deleteJson(route('agendamentos.destroy', $agendamento->id_agendamento));

        $response->assertStatus(403);
    }
}