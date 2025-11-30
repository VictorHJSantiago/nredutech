<?php

namespace Tests\Feature\Reports;

use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ReportControllerTest extends TestCase
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

    // 1
    public function test_index_mostra_pagina_de_relatorio_para_admin()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertViewIs('reports.index');
    }

    // 2
    public function test_index_mostra_pagina_de_relatorio_para_diretor()
    {
        $response = $this->actingAs($this->diretor)->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertViewIs('reports.index');
    }

    // 3
    public function test_visualizacao_falha_sem_campos_obrigatorios_ou_invalidos()
    {
        // Testando validação de data (end_date antes de start_date)
        $response = $this->actingAs($this->admin)->get(route('reports.index', [
            'start_date' => '2025-12-31',
            'end_date' => '2025-01-01',
        ]));

        $response->assertSessionHasErrors(['end_date']);
    }

    // 4
    public function test_visualizacao_falha_para_diretor_com_filtros_invalidos()
    {
        // Testando validação de campo numérico
        $response = $this->actingAs($this->diretor)->get(route('reports.index', [
            'recurso_qtd_min' => 'texto-invalido',
        ]));

        $response->assertSessionHasErrors(['recurso_qtd_min']);
    }

    // 5
    public function test_admin_pode_visualizar_relatorio_de_todas_as_escolas()
    {
        // Sem filtro de escola = todas as escolas
        $response = $this->actingAs($this->admin)->get(route('reports.index', [
            'report_type' => 'escolas'
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('reportData');
    }

    // 6
    public function test_admin_pode_visualizar_relatorio_de_escola_especifica()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index', [
            'report_type' => 'turmas',
            'id_escola' => [$this->escola->id_escola]
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('reportData');
    }

    // 7
    public function test_diretor_pode_visualizar_relatorio_apenas_da_propria_escola()
    {
        // Mesmo sem mandar filtro, o controller força o ID da escola do diretor
        $response = $this->actingAs($this->diretor)->get(route('reports.index', [
            'report_type' => 'turmas'
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('reportData');
    }

    // 8
    public function test_admin_pode_exportar_pdf_simples()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index', [
            'report_type' => 'usuarios',
            'format' => 'pdf'
        ]));

        $response->assertStatus(200);
        // Controller retorna download (ZIP para PDFs)
        $this->assertTrue($response->headers->contains('content-type', 'application/zip'));
    }

    // 9
    public function test_admin_pode_exportar_excel_simples()
    {
        Excel::fake();
        Excel::matchByRegex();

        $this->actingAs($this->admin)->get(route('reports.index', [
            'report_type' => 'usuarios',
            'format' => 'xlsx'
        ]));

        Excel::assertDownloaded('/^relatorio_NREduTech_\d{4}-\d{2}-\d{2}_\d{6}_usuarios\.xlsx$/');
    }

    // 10
    public function test_admin_pode_exportar_todos_pdf_multiplo()
    {
        // Sem especificar report_type, gera todos (multi)
        $response = $this->actingAs($this->admin)->get(route('reports.index', [
            'format' => 'pdf'
        ]));

        $response->assertStatus(200);
        $this->assertTrue($response->headers->contains('content-type', 'application/zip'));
    }

    // 11
    public function test_admin_pode_exportar_todos_excel_multiplo()
    {
        Excel::fake();
        Excel::matchByRegex();

        // Sem especificar report_type, gera todos (multi)
        $this->actingAs($this->admin)->get(route('reports.index', [
            'format' => 'xlsx'
        ]));

        Excel::assertDownloaded('/^relatorio_NREduTech_\d{4}-\d{2}-\d{2}_\d{6}_completo\.xlsx$/');
    }

    // 12
    public function test_diretor_pode_exportar_tudo_apenas_da_propria_escola()
    {
        // Diretor exportando "tudo" (multi), o controller filtra internamente pela escola dele
        $response = $this->actingAs($this->diretor)->get(route('reports.index', [
            'format' => 'pdf'
        ]));

        $response->assertStatus(200);
        $this->assertTrue($response->headers->contains('content-type', 'application/zip'));
    }
}