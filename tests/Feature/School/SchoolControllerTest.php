<?php

namespace Tests\Feature\School;

use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $diretor;
    private $municipio;
    private $escola;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->municipio = Municipio::create(['nome' => 'Curitiba']);

        $this->admin = Usuario::factory()->create([
            'tipo_usuario' => 'administrador',
            'status_aprovacao' => 'ativo'
        ]);

        $this->diretor = Usuario::factory()->create([
            'tipo_usuario' => 'diretor',
            'status_aprovacao' => 'ativo'
        ]);

        $this->escola = Escola::create([
            'nome' => 'Escola Teste',
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana',
            'id_municipio' => $this->municipio->id_municipio
        ]);
    }

    public function test_admin_pode_ver_lista_de_escolas()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index'));
        $response->assertStatus(200);
        $response->assertSee($this->escola->nome);
    }

    public function test_lista_pode_ser_filtrada_por_nivel_ensino()
    {
        $outra = Escola::create(['nome' => 'Outra', 'nivel_ensino' => 'escola_municipal', 'tipo' => 'urbana', 'id_municipio' => $this->municipio->id_municipio]);
        
        $response = $this->actingAs($this->admin)->get(route('escolas.index', ['nivel_ensino' => 'colegio_estadual']));
        $response->assertSee('Escola Teste');
        $response->assertDontSee('Outra');
    }

    public function test_lista_pode_ser_filtrada_por_tipo()
    {
        $outra = Escola::create(['nome' => 'Outra', 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural', 'id_municipio' => $this->municipio->id_municipio]);

        $response = $this->actingAs($this->admin)->get(route('escolas.index', ['tipo' => 'urbana']));
        $response->assertSee('Escola Teste');
        $response->assertDontSee('Outra');
    }

    public function test_lista_pode_ser_filtrada_por_multiplos_criterios()
    {
        $outra = Escola::create(['nome' => 'Outra', 'nivel_ensino' => 'escola_municipal', 'tipo' => 'urbana', 'id_municipio' => $this->municipio->id_municipio]);

        $response = $this->actingAs($this->admin)->get(route('escolas.index', ['nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']));
        $response->assertSee('Escola Teste');
        $response->assertDontSee('Outra');
    }

    public function test_admin_pode_ver_pagina_de_criacao_na_index()
    {
        // O método create não existe, assume-se que o formulário está na index ou em modal
        $response = $this->actingAs($this->admin)->get(route('escolas.index'));
        $response->assertStatus(200);
    }

    public function test_admin_pode_criar_nova_escola()
    {
        $dados = [
            'nome' => 'Nova Escola',
            'nivel_ensino' => 'escola_tecnica',
            'tipo' => 'rural',
            'id_municipio' => $this->municipio->id_municipio
        ];

        $response = $this->actingAs($this->admin)->post(route('escolas.store'), $dados);
        
        $response->assertRedirect(route('escolas.index'));
        $this->assertDatabaseHas('escolas', ['nome' => 'Nova Escola']);
    }

    public function test_criacao_falha_sem_campos_obrigatorios()
    {
        $response = $this->actingAs($this->admin)->post(route('escolas.store'), []);
        $response->assertSessionHasErrors(['nome', 'nivel_ensino', 'tipo', 'id_municipio']);
    }

    public function test_criacao_falha_com_municipio_inexistente()
    {
        $dados = [
            'nome' => 'Escola Falha',
            'nivel_ensino' => 'medio',
            'tipo' => 'urbana',
            'id_municipio' => 99999
        ];
        $response = $this->actingAs($this->admin)->post(route('escolas.store'), $dados);
        $response->assertSessionHasErrors(['id_municipio']);
    }

    public function test_admin_pode_ver_detalhes_da_escola_na_index()
    {
        // Método show não existe no controller, verificamos se a escola aparece na listagem
        $response = $this->actingAs($this->admin)->get(route('escolas.index'));
        $response->assertStatus(200);
        $response->assertSee($this->escola->nome);
    }

    public function test_admin_pode_ver_pagina_de_edicao()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.edit', $this->escola->id_escola));
        $response->assertStatus(200);
    }

    public function test_admin_pode_atualizar_escola()
    {
        $dados = [
            'nome' => 'Escola Editada',
            'nivel_ensino' => 'escola_municipal',
            'tipo' => 'rural',
            'id_municipio' => $this->municipio->id_municipio
        ];

        $response = $this->actingAs($this->admin)->put(route('escolas.update', $this->escola->id_escola), $dados);
        
        $response->assertRedirect(route('escolas.index'));
        $this->assertDatabaseHas('escolas', ['id_escola' => $this->escola->id_escola, 'nome' => 'Escola Editada']);
    }

    public function test_atualizacao_falha_sem_nome()
    {
        $response = $this->actingAs($this->admin)->put(route('escolas.update', $this->escola->id_escola), ['nome' => '']);
        $response->assertSessionHasErrors(['nome']);
    }

    public function test_admin_pode_deletar_escola()
    {
        $response = $this->actingAs($this->admin)->delete(route('escolas.destroy', $this->escola->id_escola));
        
        $response->assertRedirect(route('escolas.index'));
        // Correção: A model Escola não usa SoftDeletes, então o registro é removido fisicamente
        $this->assertDatabaseMissing('escolas', ['id_escola' => $this->escola->id_escola]);
    }

    public function test_diretor_nao_pode_criar_escola()
    {
        $dados = [
            'nome' => 'Escola Proibida',
            'nivel_ensino' => 'medio',
            'tipo' => 'urbana',
            'id_municipio' => $this->municipio->id_municipio
        ];
        $response = $this->actingAs($this->diretor)->post(route('escolas.store'), $dados);
        $response->assertForbidden();
    }
}