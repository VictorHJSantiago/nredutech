<?php

namespace Tests\Feature\Reports; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\RecursoDidatico;
use App\Models\Turma;
use App\Models\Agendamento; 
use App\Models\OfertaComponente; 
use App\Models\ComponenteCurricular; 
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\ReportExport; 

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $escolaDiretor;
    protected $municipio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->municipio = Municipio::factory()->create();
        $this->escolaDiretor = Escola::factory()->create(['id_municipio' => $this->municipio->id_municipio, 'nome' => 'Escola do Diretor']);
        $outraEscola = Escola::factory()->create(['id_municipio' => $this->municipio->id_municipio, 'nome' => 'Outra Escola']);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaDiretor->id_escola]);
        Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaDiretor->id_escola, 'nome_completo' => 'Professor A']); 
        Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $outraEscola->id_escola, 'nome_completo' => 'Professor B']);
        RecursoDidatico::factory()->create(['status' => 'funcionando', 'nome' => 'Recurso Funcionando']); 
        RecursoDidatico::factory()->create(['status' => 'em_manutencao', 'nome' => 'Recurso Manutencao']);
    }

    /** @test */
    public function admin_pode_acessar_pagina_de_relatorios()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertViewIs('reports.index');
        $response->assertSee('Gerar Relatório');
    }

    /** @test */
    public function diretor_pode_acessar_pagina_de_relatorios()
    {
        $response = $this->actingAs($this->diretor)->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertViewIs('reports.index');
        $response->assertDontSee('id="filter-municipio"');
        $response->assertDontSee('id="filter-instituicao"');
    }

    /** @test */
    public function gerar_relatorio_de_usuarios_para_admin_sem_filtro_mostra_todos()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index', ['tipo_relatorio' => 'usuarios']));
        $response->assertStatus(200);
        $response->assertViewHas('results');
        $response->assertSee('Professor A'); 
        $response->assertSee('Professor B'); 
        $response->assertSee($this->admin->nome_completo);
        $response->assertSee($this->diretor->nome_completo);
    }

     /** @test */
    public function gerar_relatorio_de_usuarios_para_diretor_mostra_apenas_sua_escola_e_admins()
    {
        $response = $this->actingAs($this->diretor)->get(route('reports.index', ['tipo_relatorio' => 'usuarios']));
        $response->assertStatus(200);
        $response->assertViewHas('results');
        $response->assertSee('Professor A'); 
        $response->assertDontSee('Professor B'); 
        $response->assertSee($this->admin->nome_completo); 
        $response->assertSee($this->diretor->nome_completo); 
    }

    /** @test */
    public function gerar_relatorio_de_recursos_com_filtro_status()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index', [
            'tipo_relatorio' => 'recursos',
            'status_recurso' => 'funcionando' 
        ]));
        $response->assertStatus(200);
        $response->assertViewHas('results');
        $response->assertSee('Recurso Funcionando');
        $response->assertDontSee('Recurso Manutencao');
    }

    /** @test */
    public function gerar_relatorio_com_tipo_invalido_mostra_erro_ou_padrao()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index', ['tipo_relatorio' => 'tipo_inexistente']));
        $response->assertStatus(200);
        $response->assertSee('Resultados do Relatório'); 
    }

    /** @test */
    public function admin_pode_baixar_relatorio_xlsx()
    {
        Excel::fake(); 
        $response = $this->actingAs($this->admin)->get(route('reports.index', [
            'tipo_relatorio' => 'usuarios',
            'formato' => 'xlsx' 
        ]));

        $response->assertStatus(200);
        // Excel::assertDownloaded('relatorio_usuarios.xlsx', function(ReportExport $export) { // Ou AllReportsExport
        //    return true;
        // });
         $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /** @test */
    public function diretor_pode_baixar_relatorio_pdf_da_sua_escola()
    {
        $response = $this->actingAs($this->diretor)->get(route('reports.index', [
            'tipo_relatorio' => 'recursos',
            'formato' => 'pdf' 
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

     /** @test */
    public function baixar_relatorio_com_formato_invalido_retorna_erro_ou_ignora()
    {
        Excel::fake();
        $response = $this->actingAs($this->admin)->get(route('reports.index', [
            'tipo_relatorio' => 'usuarios',
            'formato' => 'docx' 
        ]));
        $response->assertStatus(200);
        $response->assertViewIs('reports.index');
        Excel::assertNothingDownloaded(); 

        // $response->assertStatus(400);
        // $response->assertSee('Formato de exportação inválido');
    }
}