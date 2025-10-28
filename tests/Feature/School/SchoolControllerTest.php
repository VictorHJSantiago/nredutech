<?php

namespace Tests\Feature\School; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\Usuario as User; 

class SchoolControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $municipio1;
    protected $municipio2;
    protected $escola1; 
    protected $escola2; 
    protected $escola3; 

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->municipio1 = Municipio::factory()->create(['nome' => 'Municipio A']);
        $this->municipio2 = Municipio::factory()->create(['nome' => 'Municipio B']);
        $this->escola1 = Escola::factory()->create(['id_municipio' => $this->municipio1->id_municipio, 'nome' => 'Escola Alpha', 'nivel_ensino' => 'medio', 'localizacao' => 'urbana']);
        $this->escola2 = Escola::factory()->create(['id_municipio' => $this->municipio1->id_municipio, 'nome' => 'Escola Beta', 'nivel_ensino' => 'fundamental_2', 'localizacao' => 'rural']);
        $this->escola3 = Escola::factory()->create(['id_municipio' => $this->municipio2->id_municipio, 'nome' => 'Escola Gamma', 'nivel_ensino' => 'medio', 'localizacao' => 'urbana']);
    }

    /** @test */
    public function index_lista_todas_escolas_e_municipios_para_admin()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('schools.index');
        $response->assertSee($this->escola1->nome);
        $response->assertSee($this->escola2->nome);
        $response->assertSee($this->escola3->nome);
        $response->assertSee($this->municipio1->nome);
        $response->assertSee($this->municipio2->nome);
        $response->assertViewHas('escolas', fn($e) => $e->total() === 3);
        $response->assertViewHas('municipios', fn($m) => $m->count() === 2);
    }

    /** @test */
    public function index_filtra_escolas_por_nome()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index', ['search_nome' => 'Alpha']));
        $response->assertStatus(200);
        $response->assertSee($this->escola1->nome);
        $response->assertDontSee($this->escola2->nome);
        $response->assertDontSee($this->escola3->nome);
    }

    /** @test */
    public function index_filtra_escolas_por_municipio()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index', ['search_municipio' => $this->municipio2->id_municipio]));
        $response->assertStatus(200);
        $response->assertDontSee($this->escola1->nome);
        $response->assertDontSee($this->escola2->nome);
        $response->assertSee($this->escola3->nome);
    }

    /** @test */
    public function index_filtra_escolas_por_nivel_ensino()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index', ['search_nivel' => 'fundamental_2']));
        $response->assertStatus(200);
        $response->assertDontSee($this->escola1->nome);
        $response->assertSee($this->escola2->nome);
        $response->assertDontSee($this->escola3->nome);
    }

    /** @test */
    public function index_filtra_escolas_por_localizacao()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index', ['search_localizacao' => 'rural']));
        $response->assertStatus(200);
        $response->assertDontSee($this->escola1->nome);
        $response->assertSee($this->escola2->nome);
        $response->assertDontSee($this->escola3->nome);
    }

     /** @test */
    public function index_filtra_escolas_combinando_filtros()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index', [
            'search_nivel' => 'medio',
            'search_localizacao' => 'urbana'
        ]));
        $response->assertStatus(200);
        $response->assertSee($this->escola1->nome);
        $response->assertDontSee($this->escola2->nome);
        $response->assertSee($this->escola3->nome);
    }

    /** @test */
    public function administrador_pode_criar_escola_com_sucesso() 
    {
        $dadosEscola = [
            'nome' => 'Escola Delta',
            'id_municipio' => $this->municipio1->id_municipio,
            'nivel_ensino' => 'medio',
            'localizacao' => 'urbana',
        ];
        $response = $this->actingAs($this->admin)->post(route('escolas.store'), $dadosEscola);

        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('escolas', ['nome' => $dadosEscola['nome']]);
    }

    /** @test */
    public function store_falha_se_nome_faltar() 
    {
        $dadosEscola = [
            //'nome' => 'Escola Sem Nome',
            'id_municipio' => $this->municipio1->id_municipio,
            'nivel_ensino' => 'medio',
            'localizacao' => 'urbana',
        ];
        $response = $this->actingAs($this->admin)->post(route('escolas.store'), $dadosEscola);
        $response->assertSessionHasErrors('nome');
        $this->assertDatabaseMissing('escolas', ['id_municipio' => $dadosEscola['id_municipio']]); 
    }

    /** @test */
    public function store_falha_se_municipio_nao_existir()
    {
        $dadosEscola = [
            'nome' => 'Escola Municipio Invalido',
            'id_municipio' => 999, 
            'nivel_ensino' => 'medio',
            'localizacao' => 'urbana',
        ];
        $response = $this->actingAs($this->admin)->post(route('escolas.store'), $dadosEscola);
        $response->assertSessionHasErrors('id_municipio');
    }

    /** @test */
    public function administrador_pode_ver_pagina_edicao_escola() 
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.edit', $this->escola1));
        $response->assertStatus(200);
        $response->assertViewIs('schools.edit');
        $response->assertViewHas('escola', $this->escola1);
    }

    /** @test */
    public function administrador_pode_atualizar_escola_com_sucesso() 
    {
        $novoMunicipio = Municipio::factory()->create();
        $dadosAtualizados = [
            'nome' => 'Escola Alpha Renovada',
            'id_municipio' => $novoMunicipio->id_municipio,
            'nivel_ensino' => 'fundamental_1',
            'localizacao' => 'rural',
        ];
        $response = $this->actingAs($this->admin)->put(route('escolas.update', $this->escola1), $dadosAtualizados);

        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('escolas', [
            'id_escola' => $this->escola1->id_escola,
            'nome' => $dadosAtualizados['nome'],
            'id_municipio' => $novoMunicipio->id_municipio,
            'nivel_ensino' => 'fundamental_1',
            'localizacao' => 'rural'
        ]);
    }

    /** @test */
    public function update_falha_se_nome_for_removido()
    {
        $dadosAtualizados = [
            'nome' => '', 
            'id_municipio' => $this->escola1->id_municipio,
            'nivel_ensino' => $this->escola1->nivel_ensino,
            'localizacao' => $this->escola1->localizacao,
        ];
        $response = $this->actingAs($this->admin)->put(route('escolas.update', $this->escola1), $dadosAtualizados);
        $response->assertSessionHasErrors('nome');
        $this->assertDatabaseHas('escolas', ['id_escola' => $this->escola1->id_escola, 'nome' => $this->escola1->nome]); 
    }

    /** @test */
    public function update_falha_se_municipio_enviado_nao_existir()
    {
         $dadosAtualizados = ['id_municipio' => 987]; 
        $response = $this->actingAs($this->admin)->put(route('escolas.update', $this->escola1), $dadosAtualizados);
        $response->assertSessionHasErrors('id_municipio');
    }

    /** @test */
    public function administrador_pode_excluir_escola_sem_dependencias() 
    {
        $escolaParaExcluir = Escola::factory()->create(['id_municipio' => $this->municipio1->id_municipio]);
        $response = $this->actingAs($this->admin)->delete(route('escolas.destroy', $escolaParaExcluir));
        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('escolas', ['id_escola' => $escolaParaExcluir->id_escola]);
    }

    /** @test */
    public function administrador_nao_pode_excluir_escola_com_turmas() 
    {
        Turma::factory()->create(['id_escola' => $this->escola1->id_escola]);
        $response = $this->actingAs($this->admin)->delete(route('escolas.destroy', $this->escola1));
        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('escolas', ['id_escola' => $this->escola1->id_escola]);
    }

     /** @test */
    public function administrador_nao_pode_excluir_escola_com_usuarios() 
    {
        User::factory()->create(['id_escola' => $this->escola1->id_escola, 'tipo_usuario' => 'professor']);
        $response = $this->actingAs($this->admin)->delete(route('escolas.destroy', $this->escola1));
        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('escolas', ['id_escola' => $this->escola1->id_escola]);
    }

}