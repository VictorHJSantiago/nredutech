<?php

namespace Tests\Feature\School; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\Usuario as User; 
use PHPUnit\Framework\Attributes\Test;

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
        $this->municipio1 = Municipio::create(['nome' => 'Municipio A', 'estado' => 'PR']);
        $this->municipio2 = Municipio::create(['nome' => 'Municipio B', 'estado' => 'SC']);
        
        $this->escola1 = Escola::create(['id_municipio' => $this->municipio1->id_municipio, 'nome' => 'Escola Alpha', 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        $this->escola2 = Escola::create(['id_municipio' => $this->municipio1->id_municipio, 'nome' => 'Escola Beta', 'nivel_ensino' => 'escola_municipal', 'tipo' => 'rural']);
        $this->escola3 = Escola::create(['id_municipio' => $this->municipio2->id_municipio, 'nome' => 'Escola Gamma', 'nivel_ensino' => 'escola_tecnica', 'tipo' => 'urbana']);
    }

    #[Test]
    public function test_index_lista_todas_escolas_e_municipios_para_admin()
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

    #[Test]
    public function test_index_filtra_escolas_por_nome()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index', ['search_nome' => 'Alpha']));
        $response->assertStatus(200);
        $response->assertSee($this->escola1->nome);
        $response->assertDontSee($this->escola2->nome);
        $response->assertDontSee($this->escola3->nome);
    }

    #[Test]
    public function test_index_filtra_escolas_por_municipio()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index', ['id_municipio' => $this->municipio2->id_municipio]));
        $response->assertStatus(200);
        $response->assertDontSee($this->escola1->nome);
        $response->assertDontSee($this->escola2->nome);
        $response->assertSee($this->escola3->nome);
    }

    #[Test]
    public function test_index_filtra_escolas_por_nivel_ensino()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index', ['nivel_ensino' => 'escola_municipal']));
        $response->assertStatus(200);
        $response->assertDontSee($this->escola1->nome);
        $response->assertSee($this->escola2->nome);
        $response->assertDontSee($this->escola3->nome);
    }

    #[Test]
    public function test_index_filtra_escolas_por_localizacao()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index', ['tipo' => 'rural']));
        $response->assertStatus(200);
        $response->assertDontSee($this->escola1->nome);
        $response->assertSee($this->escola2->nome);
        $response->assertDontSee($this->escola3->nome);
    }

    #[Test]
    public function test_index_filtra_escolas_combinando_filtros()
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.index', [
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]));
        $response->assertStatus(200);
        $response->assertSee($this->escola1->nome);
        $response->assertDontSee($this->escola2->nome);
        $response->assertDontSee($this->escola3->nome);
    }

    #[Test]
    public function test_administrador_pode_criar_escola_com_sucesso() 
    {
        $dadosEscola = [
            'nome' => 'Escola Delta',
            'id_municipio' => $this->municipio1->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana',
        ];
        $response = $this->actingAs($this->admin)->post(route('escolas.store'), $dadosEscola);

        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('escolas', ['nome' => $dadosEscola['nome']]);
    }

    #[Test]
    public function test_store_falha_se_nome_faltar() 
    {
        $dadosEscola = [
            'id_municipio' => $this->municipio1->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana',
        ];
        $response = $this->actingAs($this->admin)->post(route('escolas.store'), $dadosEscola);
        $response->assertSessionHasErrors('nome');
        $this->assertDatabaseCount('escolas', 3);
    }

    #[Test]
    public function test_store_falha_se_municipio_nao_existir()
    {
        $dadosEscola = [
            'nome' => 'Escola Municipio Invalido',
            'id_municipio' => 999, 
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana',
        ];
        $response = $this->actingAs($this->admin)->post(route('escolas.store'), $dadosEscola);
        $response->assertSessionHasErrors('id_municipio');
    }

    #[Test]
    public function test_administrador_pode_ver_pagina_edicao_escola() 
    {
        $response = $this->actingAs($this->admin)->get(route('escolas.edit', $this->escola1));
        $response->assertStatus(200);
        $response->assertViewIs('schools.edit');
        $response->assertViewHas('escola', $this->escola1);
    }

    #[Test]
    public function test_administrador_pode_atualizar_escola_com_sucesso() 
    {
        $novoMunicipio = Municipio::create(['nome' => 'Municipio C', 'estado' => 'SP']);
        $dadosAtualizados = [
            'nome' => 'Escola Alpha Renovada',
            'id_municipio' => $novoMunicipio->id_municipio,
            'nivel_ensino' => 'escola_municipal',
            'tipo' => 'rural',
        ];
        $response = $this->actingAs($this->admin)->put(route('escolas.update', $this->escola1), $dadosAtualizados);

        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('escolas', [
            'id_escola' => $this->escola1->id_escola,
            'nome' => $dadosAtualizados['nome'],
            'id_municipio' => $novoMunicipio->id_municipio,
            'nivel_ensino' => 'escola_municipal',
            'tipo' => 'rural'
        ]);
    }

    #[Test]
    public function test_update_falha_se_nome_for_removido()
    {
        $dadosAtualizados = [
            'nome' => '', 
            'id_municipio' => $this->escola1->id_municipio,
            'nivel_ensino' => $this->escola1->nivel_ensino,
            'tipo' => $this->escola1->tipo,
        ];
        $response = $this->actingAs($this->admin)->put(route('escolas.update', $this->escola1), $dadosAtualizados);
        $response->assertSessionHasErrors('nome');
        $this->assertDatabaseHas('escolas', ['id_escola' => $this->escola1->id_escola, 'nome' => $this->escola1->nome]); 
    }

    #[Test]
    public function test_update_falha_se_municipio_enviado_nao_existir()
    {
         $dadosAtualizados = [
            'nome' => $this->escola1->nome,
            'id_municipio' => 987,
            'nivel_ensino' => $this->escola1->nivel_ensino,
            'tipo' => $this->escola1->tipo,
        ]; 
        $response = $this->actingAs($this->admin)->put(route('escolas.update', $this->escola1), $dadosAtualizados);
        $response->assertSessionHasErrors('id_municipio');
    }

    #[Test]
    public function test_administrador_pode_excluir_escola_sem_dependencias() 
    {
        $escolaParaExcluir = Escola::create(['nome' => 'Escola Para Excluir', 'id_municipio' => $this->municipio1->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        $response = $this->actingAs($this->admin)->delete(route('escolas.destroy', $escolaParaExcluir));
        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('escolas', ['id_escola' => $escolaParaExcluir->id_escola]);
    }

    #[Test]
    public function test_administrador_nao_pode_excluir_escola_com_turmas() 
    {
        Turma::factory()->create(['id_escola' => $this->escola1->id_escola]);
        $response = $this->actingAs($this->admin)->delete(route('escolas.destroy', $this->escola1));
        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('escolas', ['id_escola' => $this->escola1->id_escola]);
    }

    #[Test]
    public function test_administrador_nao_pode_excluir_escola_com_usuarios() 
    {
        User::factory()->create(['id_escola' => $this->escola1->id_escola, 'tipo_usuario' => 'professor']);
        $response = $this->actingAs($this->admin)->delete(route('escolas.destroy', $this->escola1));
        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('escolas', ['id_escola' => $this->escola1->id_escola]);
    }
}