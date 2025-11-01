<?php

namespace Tests\Feature\Reports; // Namespace correto

use Tests\TestCase; // Classe base
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;

class ReportRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $escola->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $escola->id_escola]);
    }

    /** @test */
    public function guest_e_redirecionado_da_rota_de_relatorios()
    {
        $response = $this->get(route('reports.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_pode_acessar_rota_de_relatorios()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertViewIs('reports.index');
    }

    /** @test */
    public function diretor_pode_acessar_rota_de_relatorios()
    {
        $response = $this->actingAs($this->diretor)->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertViewIs('reports.index');
    }

    /** @test */
    public function professor_e_bloqueado_da_rota_de_relatorios()
    {
        $response = $this->actingAs($this->professor)->get(route('reports.index'));
        
        // Esta rota (GET /relatorios) é protegida apenas pelo layout (app.blade.php).
        // A rota em si (web.php) não tem middleware 'can:administrador' ou 'can:diretor'.
        // ISSO É UMA FALHA DE SEGURANÇA.
        // O teste correto para o código atual é 200, mas ele não deveria ser.
        
        // $response->assertStatus(200); // Teste para o código atual (INSEGURO)

        // Teste para o código IDEAL (após adicionar middleware na rota):
        $response->assertStatus(403); // Ou assertRedirect(route('index'))
    }
}