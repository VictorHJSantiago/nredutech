<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\RecursoDidatico;
use App\Models\Agendamento;
use App\Models\OfertaComponente;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;
    private Escola $escola;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->escola = Escola::factory()->create();
        
        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretor = Usuario::factory()->diretor()->create(['id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->professor()->create(['id_escola' => $this->escola->id_escola]);

        Turma::factory()->count(3)->create(['id_escola' => $this->escola->id_escola]);
        RecursoDidatico::factory()->count(5)->create(['id_escola' => $this->escola->id_escola]);
        $oferta = OfertaComponente::factory()->create([
            'id_turma' => Turma::factory()->create(['id_escola' => $this->escola->id_escola])->id_turma,
            'id_professor' => $this->professor->id_usuario,
        ]);
        Agendamento::factory()->count(2)->create([
            'id_oferta' => $oferta->id_oferta,
            'data_hora_inicio' => now()->addDay(),
        ]);
        
        Agendamento::factory()->count(4)->create([
            'id_oferta' => $oferta->id_oferta,
            'data_hora_inicio' => now()->subDay(),
        ]);
    }

    public function test_admin_dashboard_shows_system_wide_kpis()
    {
        $response = $this->actingAs($this->admin)->get(route('index'));

        $response->assertOk();
        $response->assertViewIs('index');
        $response->assertViewHas('kpis');
        
        $kpis = $response->viewData('kpis');
        $this->assertEquals(Usuario::count(), $kpis['total_usuarios']);
        $this->assertEquals(Escola::count(), $kpis['total_escolas']);
        $this->assertEquals(RecursoDidatico::count(), $kpis['total_recursos']);
        $this->assertEquals(2, $kpis['total_agendamentos_futuros']);
        $this->assertEquals(4, $kpis['total_agendamentos_passados']);
    }

    public function test_diretor_dashboard_shows_school_specific_kpis()
    {
        Escola::factory()->create();
        RecursoDidatico::factory()->count(10)->create();
        
        $response = $this->actingAs($this->diretor)->get(route('index'));

        $response->assertOk();
        $response->assertViewIs('index');
        $response->assertViewHas('kpis');
        
        $kpis = $response->viewData('kpis');
        $this->assertEquals(Usuario::where('id_escola', $this->escola->id_escola)->count(), $kpis['total_usuarios']);
        $this->assertEquals(1, $kpis['total_escolas']);
        $this->assertEquals(5, $kpis['total_recursos']);
        $this->assertEquals(2, $kpis['total_agendamentos_futuros']);
        $this->assertEquals(4, $kpis['total_agendamentos_passados']);
    }

    public function test_professor_dashboard_shows_personal_kpis()
    {
        OfertaComponente::factory()->create([
            'id_turma' => Turma::factory()->create(['id_escola' => $this->escola->id_escola])->id_turma,
            'id_professor' => $this->diretor->id_usuario,
        ]);
        
        $response = $this->actingAs($this->professor)->get(route('index'));

        $response->assertOk();
        $response->assertViewIs('index');
        $response->assertViewHas('kpis');
        
        $kpis = $response->viewData('kpis');
        $this->assertEquals(1, $kpis['total_ofertas']);
        $this->assertEquals(5, $kpis['total_recursos']);
        $this->assertEquals(2, $kpis['total_agendamentos_futuros']);
        $this->assertEquals(4, $kpis['total_agendamentos_passados']);
    }
}