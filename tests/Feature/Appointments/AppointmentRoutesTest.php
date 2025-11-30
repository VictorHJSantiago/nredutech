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

class AppointmentRoutesTest extends TestCase
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
    private $agendamento;

    protected function setUp(): void
    {
        parent::setUp();

        $municipio = Municipio::create(['nome' => 'Curitiba']);

        $this->escola = Escola::create([
            'nome' => 'Escola Teste',
            'nivel_ensino' => 'Médio',
            'tipo' => 'Estadual',
            'id_municipio' => $municipio->id_municipio
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
            'serie' => '3º Ano',
            'ano_letivo' => 2025,
            'turno' => 'tarde',
            'nivel_escolaridade' => 'medio',
            'id_escola' => $this->escola->id_escola
        ]);

        $this->componente = ComponenteCurricular::create([
            'nome' => 'História',
            'carga_horaria' => 40,
            'status' => 'aprovado'
        ]);

        $this->oferta = OfertaComponente::create([
            'id_turma' => $this->turma->id_turma,
            'id_professor' => $this->professor->id_usuario,
            'id_componente' => $this->componente->id_componente
        ]);

        $this->recurso = RecursoDidatico::create([
            'nome' => 'Mapa Mundi',
            'quantidade' => 1,
            'status' => 'funcionando',
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $this->admin->id_usuario
        ]);

        $this->agendamento = Agendamento::create([
            'data_hora_inicio' => now()->addDays(5)->setHour(14),
            'data_hora_fim' => now()->addDays(5)->setHour(15),
            'status' => 'agendado',
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ]);
    }

    public function test_visitante_e_redirecionado_de_todas_rotas_de_agendamento()
    {
        $this->get(route('agendamentos.index'))->assertRedirect(route('login'));
        $this->getJson(route('appointments.events'))->assertStatus(401);
        $this->postJson(route('agendamentos.store'), [])->assertStatus(401);
        $this->deleteJson(route('agendamentos.destroy', $this->agendamento->id_agendamento))->assertStatus(401);
    }

    public function test_admin_pode_acessar_todas_rotas_de_agendamento()
    {
        $this->actingAs($this->admin)->get(route('agendamentos.index'))->assertStatus(200);
        
        $this->actingAs($this->admin)->getJson(route('appointments.events', [
            'start' => now()->toDateString(), 
            'end' => now()->addDay()->toDateString()
        ]))->assertStatus(200);

        $this->actingAs($this->admin)->deleteJson(route('agendamentos.destroy', $this->agendamento->id_agendamento))->assertStatus(200);
    }

    public function test_diretor_pode_acessar_todas_rotas_de_agendamento()
    {
        $this->actingAs($this->diretor)->get(route('agendamentos.index'))->assertStatus(200);

        $this->actingAs($this->diretor)->getJson(route('appointments.events', [
            'start' => now()->toDateString(), 
            'end' => now()->addDay()->toDateString()
        ]))->assertStatus(200);

        $this->actingAs($this->diretor)->deleteJson(route('agendamentos.destroy', $this->agendamento->id_agendamento))->assertStatus(200);
    }

    public function test_professor_pode_acessar_rotas_e_criar()
    {
        $this->actingAs($this->professor)->get(route('agendamentos.index'))->assertStatus(200);

        $dados = [
            'data_hora_inicio' => now()->addDays(10)->setHour(10)->format('Y-m-d H:i:s'),
            'data_hora_fim' => now()->addDays(10)->setHour(11)->format('Y-m-d H:i:s'),
            'id_oferta' => $this->oferta->id_oferta,
            'id_recurso' => $this->recurso->id_recurso
        ];

        $this->actingAs($this->professor)->postJson(route('agendamentos.store'), $dados)->assertStatus(201);
    }

    public function test_professor_pode_destruir_proprio_agendamento()
    {
        $this->actingAs($this->professor)
             ->deleteJson(route('agendamentos.destroy', $this->agendamento->id_agendamento))
             ->assertStatus(200);
    }

    public function test_professor_e_proibido_de_destruir_agendamento_de_outro()
    {
        $outroProf = Usuario::factory()->create([
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escola->id_escola
        ]);
        
        $this->actingAs($outroProf)
             ->deleteJson(route('agendamentos.destroy', $this->agendamento->id_agendamento))
             ->assertStatus(403);
    }
}