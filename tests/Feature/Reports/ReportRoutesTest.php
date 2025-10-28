<?php

namespace Tests\Feature\Reports; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
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
    public function admin_pode_acessar_rota_index_relatorios()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function diretor_pode_acessar_rota_index_relatorios()
    {
        $response = $this->actingAs($this->diretor)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function professor_nao_pode_acessar_rota_index_relatorios()
    {
        $response = $this->actingAs($this->professor)->get(route('reports.index'));
        // $response->assertStatus(403);
        $response->assertRedirect(route('index'));
    }

    /** @test */
    public function guest_e_redirecionado_da_rota_index_relatorios()
    {
        $response = $this->get(route('reports.index'));
        $response->assertRedirect(route('login'));
    }
}