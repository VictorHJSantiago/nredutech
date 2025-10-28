<?php

namespace Tests\Feature\School; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;

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
        $this->municipio = Municipio::factory()->create();
        $this->escola = Escola::factory()->create(['id_municipio' => $this->municipio->id_municipio]);
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola]);
    }

    /** @test */
    public function admin_pode_acessar_index_escolas()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function diretor_nao_pode_acessar_index_escolas()
    {
        $response = $this->actingAs($this->diretor)->get(route('escolas.index'));
        $response->assertStatus(403); 
    }

    /** @test */
    public function professor_nao_pode_acessar_index_escolas()
    {
        $response = $this->actingAs($this->professor)->get(route('escolas.index'));
        $response->assertStatus(403);
    }

     /** @test */
    public function admin_pode_acessar_edit_escola()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.edit', $this->escola));
        $response->assertStatus(200);
    }

    /** @test */
    public function diretor_nao_pode_acessar_edit_escola()
    {
        $response = $this->actingAs($this->diretor)->get(route('escolas.edit', $this->escola));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_pode_enviar_store_escola()
    {
        $dados = Escola::factory()->make(['id_municipio' => $this->municipio->id_municipio])->toArray();
        $response = $this->actingAs($this->admin)->post(route('escolas.store'), $dados);
        $response->assertRedirect(route('escolas.index')); 
    }

    /** @test */
    public function diretor_nao_pode_enviar_store_escola()
    {
        $dados = Escola::factory()->make(['id_municipio' => $this->municipio->id_municipio])->toArray();
        $response = $this->actingAs($this->diretor)->post(route('escolas.store'), $dados);
        $response->assertStatus(403);
    }

     /** @test */
    public function admin_pode_enviar_update_escola()
    {
        $dados = ['nome' => 'Nome Atualizado Escola'];
        $response = $this->actingAs($this->admin)->put(route('escolas.update', $this->escola), $dados);
        $response->assertRedirect(route('escolas.index'));
    }

    /** @test */
    public function diretor_nao_pode_enviar_update_escola()
    {
         $dados = ['nome' => 'Nome Atualizado Escola'];
        $response = $this->actingAs($this->diretor)->put(route('escolas.update', $this->escola), $dados);
        $response->assertStatus(403);
    }

     /** @test */
    public function admin_pode_enviar_destroy_escola()
    {
        $response = $this->actingAs($this->admin)->delete(route('escolas.destroy', $this->escola));
        $response->assertRedirect(route('escolas.index'));
    }

    /** @test */
    public function diretor_nao_pode_enviar_destroy_escola()
    {
        $response = $this->actingAs($this->diretor)->delete(route('escolas.destroy', $this->escola));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_pode_enviar_store_municipio()
    {
        $dados = ['nome' => 'Municipio Novo Rota'];
        $response = $this->actingAs($this->admin)->post(route('municipios.store'), $dados);
        $response->assertRedirect(route('escolas.index'));
    }

     /** @test */
    public function diretor_nao_pode_enviar_store_municipio()
    {
        $dados = ['nome' => 'Municipio Novo Rota'];
        $response = $this->actingAs($this->diretor)->post(route('municipios.store'), $dados);
        $response->assertStatus(403);
    }

}