<?php

namespace Tests\Feature\School; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use PHPUnit\Framework\Attributes\Test;

class SchoolRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
    protected $escola;
    protected $municipio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->municipio = Municipio::create(['nome' => 'Municipio Rota', 'estado' => 'PR']);
        $this->escola = Escola::create(['nome' => 'Escola Rota', 'id_municipio' => $this->municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola]);
    }

    #[Test]
    public function test_admin_pode_acessar_index_escolas()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function test_diretor_nao_pode_acessar_index_escolas()
    {
        $response = $this->actingAs($this->diretor)->get(route('escolas.index'));
        $response->assertStatus(403); 
    }

    #[Test]
    public function test_professor_nao_pode_acessar_index_escolas()
    {
        $response = $this->actingAs($this->professor)->get(route('escolas.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function test_admin_pode_acessar_edit_escola()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.edit', $this->escola));
        $response->assertStatus(200);
    }

    #[Test]
    public function test_diretor_nao_pode_acessar_edit_escola()
    {
        $response = $this->actingAs($this->diretor)->get(route('escolas.edit', $this->escola));
        $response->assertStatus(403);
    }

    #[Test]
    public function test_admin_pode_enviar_store_escola()
    {
        $dados = [
            'nome' => 'Escola Rota Teste',
            'id_municipio' => $this->municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ];
        $response = $this->actingAs($this->admin)->post(route('escolas.store'), $dados);
        $response->assertRedirect(route('escolas.index')); 
    }

    #[Test]
    public function test_diretor_nao_pode_enviar_store_escola()
    {
        $dados = [
            'nome' => 'Escola Rota Teste Diretor',
            'id_municipio' => $this->municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ];
        $response = $this->actingAs($this->diretor)->post(route('escolas.store'), $dados);
        $response->assertStatus(403);
    }

    #[Test]
    public function test_admin_pode_enviar_update_escola()
    {
        $dados = [
            'nome' => 'Nome Atualizado Escola', 
            'tipo' => 'rural',
            'id_municipio' => $this->municipio->id_municipio,
            'nivel_ensino' => $this->escola->nivel_ensino
        ];
        $response = $this->actingAs($this->admin)->put(route('escolas.update', $this->escola), $dados);
        $response->assertRedirect(route('escolas.index'));
    }

    #[Test]
    public function test_diretor_nao_pode_enviar_update_escola()
    {
         $dados = [
            'nome' => 'Nome Atualizado Escola',
            'tipo' => 'rural',
            'id_municipio' => $this->municipio->id_municipio,
            'nivel_ensino' => $this->escola->nivel_ensino
        ];
        $response = $this->actingAs($this->diretor)->put(route('escolas.update', $this->escola), $dados);
        $response->assertStatus(403);
    }

    #[Test]
    public function test_admin_pode_enviar_destroy_escola()
    {
        $novaEscola = Escola::create(['nome' => 'Escola Para Deletar', 'id_municipio' => $this->municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        $response = $this->actingAs($this->admin)->delete(route('escolas.destroy', $novaEscola));
        $response->assertRedirect(route('escolas.index'));
    }

    #[Test]
    public function test_diretor_nao_pode_enviar_destroy_escola()
    {
        $response = $this->actingAs($this->diretor)->delete(route('escolas.destroy', $this->escola));
        $response->assertStatus(403);
    }

    #[Test]
    public function test_admin_pode_enviar_store_municipio()
    {
        $dados = ['nome' => 'Municipio Novo Rota', 'estado' => 'SP'];
        $response = $this->actingAs($this->admin)->post(route('municipios.store'), $dados);
        $response->assertRedirect(route('escolas.index'));
    }

    #[Test]
    public function test_diretor_nao_pode_enviar_store_municipio()
    {
        $dados = ['nome' => 'Municipio Novo Rota', 'estado' => 'SP'];
        $response = $this->actingAs($this->diretor)->post(route('municipios.store'), $dados);
        $response->assertForbidden();
    }
}