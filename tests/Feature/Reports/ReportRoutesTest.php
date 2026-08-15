<?php

namespace Tests\Feature\Reports;

use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportRoutesTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $diretor;
    private $professor;
    private $escola;
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
    }

    public function test_visitante_e_redirecionado_de_todas_rotas_de_relatorio()
    {
        $this->get(route('reports.index'))->assertRedirect(route('login'));
    }

    public function test_professor_pode_acessar_rotas_de_relatorio()
    {
        $this->actingAs($this->professor);
        
        $this->get(route('reports.index'))->assertStatus(200);
    }

    public function test_diretor_pode_acessar_todas_rotas_de_relatorio()
    {
        $this->actingAs($this->diretor);

        $this->get(route('reports.index'))->assertStatus(200);
    }

    public function test_admin_pode_acessar_todas_rotas_de_relatorio()
    {
        $this->actingAs($this->admin);

        $this->get(route('reports.index'))->assertStatus(200);
    }
}