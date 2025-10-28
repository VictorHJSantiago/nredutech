<?php

namespace Tests\Feature\Appointments; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Agendamento;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\OfertaComponente;
use App\Models\RecursoDidatico;
use Carbon\Carbon;


class AppointmentRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $professor;
    protected $agendamento;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor']);
        $municipio = Municipio::factory()->create();
        $escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $turma = Turma::factory()->create(['id_escola' => $escola->id_escola]);
        $componente = ComponenteCurricular::factory()->create();
        $recurso = RecursoDidatico::factory()->create();
        $oferta = OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_professor' => $this->professor->id_usuario, 
            'id_componente' => $componente->id_componente
        ]);
        $this->agendamento = Agendamento::factory()->create([
            'id_recurso' => $recurso->id_recurso,
            'id_oferta' => $oferta->id_oferta,
            'data_hora_inicio' => Carbon::now()->addHours(2) 
        ]);
    }

    /**
     * @test
     * @dataProvider
     */
    public function usuarios_autenticados_podem_acessar_index_agendamentos($tipoUsuario)
    {
        $user = $this->getUserByType($tipoUsuario);
        $response = $this->actingAs($user)->get(route('agendamentos.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function guest_e_redirecionado_de_index_agendamentos()
    {
        $response = $this->get(route('agendamentos.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     * @dataProvider 
     */
    public function usuarios_autenticados_podem_acessar_api_events($tipoUsuario)
    {
        $user = $this->getUserByType($tipoUsuario);
        $start = Carbon::now()->startOfMonth()->toIso8601String();
        $end = Carbon::now()->endOfMonth()->toIso8601String();
        $response = $this->actingAs($user)->getJson(route('appointments.events', ['start' => $start, 'end' => $end]));
        $response->assertStatus(200);
    }

    /**
     * @test
     * @dataProvider 
     */
    public function usuarios_autenticados_podem_acessar_api_availability($tipoUsuario)
    {
        $user = $this->getUserByType($tipoUsuario);
        $date = Carbon::now()->addDay()->format('Y-m-d');
        $response = $this->actingAs($user)->postJson(route('appointments.availability'), ['date' => $date]);
        $response->assertStatus(200);
    }

    /**
     * @test
     * @dataProvider
     */
    public function usuarios_autenticados_podem_enviar_store_agendamento($tipoUsuario)
    {
        $user = $this->getUserByType($tipoUsuario);
        $inicio = Carbon::now()->addDays(5)->hour(10);
        $dados = [
            'data_hora_inicio' => $inicio->toDateTimeString(),
            'data_hora_fim' => $inicio->copy()->addHour()->toDateTimeString(),
            'id_recurso' => $this->agendamento->id_recurso, 
            'id_oferta' => $this->agendamento->id_oferta,   
        ];

        $response = $this->actingAs($user)->postJson(route('agendamentos.store'), $dados);
        $response->assertStatus(201);
    }

    /** @test */
    public function usuarios_autorizados_podem_enviar_destroy_agendamento()
    {
        \Illuminate\Support\Facades\Gate::shouldReceive('authorize')->with('cancelar-agendamento', $this->agendamento)->andReturn(true);
        $responseAdmin = $this->actingAs($this->admin)->deleteJson(route('agendamentos.destroy', $this->agendamento));
        $responseAdmin->assertStatus(200);

        $this->agendamento = Agendamento::factory()->create([
             'id_recurso' => $this->agendamento->id_recurso,
             'id_oferta' => $this->agendamento->id_oferta,
             'data_hora_inicio' => Carbon::now()->addHours(2)
        ]);

        \Illuminate\Support\Facades\Gate::shouldReceive('authorize')->with('cancelar-agendamento', $this->agendamento)->andReturn(true);
        $responseProf = $this->actingAs($this->professor)->deleteJson(route('agendamentos.destroy', $this->agendamento));
        $responseProf->assertStatus(200);

    }

    /** @test */
    public function usuario_nao_autorizado_nao_pode_enviar_destroy_agendamento()
    {
        $outroProfessor = Usuario::factory()->create(['tipo_usuario' => 'professor']);
        \Illuminate\Support\Facades\Gate::shouldReceive('authorize')->with('cancelar-agendamento', $this->agendamento)->andThrow(new \Illuminate\Auth\Access\AuthorizationException);
        $response = $this->actingAs($outroProfessor)->deleteJson(route('agendamentos.destroy', $this->agendamento));
        $response->assertStatus(403);
    }

    private function getUserByType(string $type): Usuario
    {
        return $type === 'administrador' ? $this->admin : $this->professor; 
    }
    public static function usuariosAutenticadosProvider(): array
    {
        return [['administrador'], ['professor']]; 
    }
}