<?php

namespace Tests\Feature\Reports;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Agendamento;
use App\Models\RecursoDidatico;
use App\Models\OfertaComponente;
use App\Exports\ReportExport;
use App\Exports\AllReportsExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretorA;
    private Escola $escolaA;
    private Escola $escolaB;

    protected function setUp(): void
    {
        parent::setUp();
        Excel::fake();
        Pdf::fake();

        $this->escolaA = Escola::factory()->create();
        $this->escolaB = Escola::factory()->create();

        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretorA = Usuario::factory()->diretor()->create(['id_escola' => $this->escolaA->id_escola]);
        
        $recursoA = RecursoDidatico::factory()->create(['id_escola' => $this->escolaA->id_escola]);
        $ofertaA = OfertaComponente::factory()->create(['id_turma' => \App\Models\Turma::factory()->create(['id_escola' => $this->escolaA->id_escola])]);
        Agendamento::factory()->count(5)->create([
            'id_recurso' => $recursoA->id_recurso,
            'id_oferta' => $ofertaA->id_oferta,
            'data_hora_inicio' => now()->subDays(5),
            'data_hora_fim' => now()->subDays(5)->addHour(),
        ]);
        
        $recursoB = RecursoDidatico::factory()->create(['id_escola' => $this->escolaB->id_escola]);
        $ofertaB = OfertaComponente::factory()->create(['id_turma' => \App\Models\Turma::factory()->create(['id_escola' => $this->escolaB->id_escola])]);
        Agendamento::factory()->count(3)->create([
            'id_recurso' => $recursoB->id_recurso,
            'id_oferta' => $ofertaB->id_oferta,
            'data_hora_inicio' => now()->subDays(2),
            'data_hora_fim' => now()->subDays(2)->addHour(),
        ]);
    }

    public function test_index_shows_report_page_for_admin()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index'));
        $response->assertOk();
        $response->assertViewIs('reports.index');
        $response->assertViewHas('escolas');
    }

    public function test_index_shows_report_page_for_diretor()
    {
        $response = $this->actingAs($this->diretorA)->get(route('reports.index'));
        $response->assertOk();
        $response->assertViewIs('reports.index');
        $response->assertViewMissing('escolas');
    }

    public function test_preview_fails_validation_without_required_fields()
    {
        $response = $this->actingAs($this->admin)->post(route('reports.preview'), []);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['report_type', 'start_date', 'end_date', 'escola_id']);
    }

    public function test_preview_fails_validation_for_diretor_without_required_fields()
    {
        $response = $this->actingAs($this->diretorA)->post(route('reports.preview'), []);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['report_type', 'start_date', 'end_date']);
        $response->assertSessionDoesntHaveErrors('escola_id');
    }

    public function test_admin_can_preview_report_for_all_schools()
    {
        $data = [
            'report_type' => 'usage_by_resource',
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'escola_id' => 'all',
        ];

        $response = $this->actingAs($this->admin)->post(route('reports.preview'), $data);
        
        $response->assertOk();
        $response->assertViewIs('reports.partials.preview');
        $response->assertViewHas('data');
        $response->assertViewHas('kpis');
        $response->assertViewHas('escolaNome', 'Todas as Escolas');
        $this->assertEquals(8, $response->viewData('kpis')['total_agendamentos']);
    }

    public function test_admin_can_preview_report_for_specific_school()
    {
        $data = [
            'report_type' => 'usage_by_resource',
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'escola_id' => $this->escolaA->id_escola,
        ];

        $response = $this->actingAs($this->admin)->post(route('reports.preview'), $data);
        
        $response->assertOk();
        $response->assertViewHas('escolaNome', $this->escolaA->nome);
        $this->assertEquals(5, $response->viewData('kpis')['total_agendamentos']);
    }

    public function test_diretor_can_preview_report_for_own_school_only()
    {
        $data = [
            'report_type' => 'usage_by_resource',
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->diretorA)->post(route('reports.preview'), $data);
        
        $response->assertOk();
        $response->assertViewHas('escolaNome', $this->escolaA->nome);
        $this->assertEquals(5, $response->viewData('kpis')['total_agendamentos']);
    }

    public function test_admin_can_export_pdf()
    {
        $data = [
            'report_type' => 'usage_by_resource',
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'escola_id' => $this->escolaA->id_escola,
            'format' => 'pdf',
        ];

        $response = $this->actingAs($this->admin)->post(route('reports.export'), $data);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        Pdf::assertViewIs('reports.partials.pdf');
    }

    public function test_admin_can_export_excel()
    {
        $data = [
            'report_type' => 'usage_by_resource',
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'escola_id' => 'all',
            'format' => 'excel',
        ];

        $response = $this->actingAs($this->admin)->post(route('reports.export'), $data);
        
        $response->assertOk();
        Excel::assertDownloaded('relatorio_nredutech.xlsx', function (ReportExport $export) {
            return $export->escolaId === 'all' && $export->reportType === 'usage_by_resource';
        });
    }

    public function test_admin_can_export_all_pdf_multi()
    {
        $data = [
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'escola_id' => 'all',
            'format' => 'pdf',
        ];
        
        $response = $this->actingAs($this->admin)->post(route('reports.exportAll'), $data);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        Pdf::assertViewIs('reports.partials.pdf_multi');
        Pdf::assertViewHas('allData');
    }

    public function test_admin_can_export_all_excel_multi()
    {
        $data = [
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'escola_id' => 'all',
            'format' => 'excel',
        ];

        $response = $this->actingAs($this->admin)->post(route('reports.exportAll'), $data);
        
        $response->assertOk();
        Excel::assertDownloaded('relatorio_completo_nredutech.xlsx', function (AllReportsExport $export) {
            return $export->escolaId === 'all';
        });
    }

    public function test_diretor_can_export_all_for_own_school_only()
    {
        $data = [
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'format' => 'excel',
        ];

        $response = $this->actingAs($this->diretorA)->post(route('reports.exportAll'), $data);
        
        $response->assertOk();
        Excel::assertDownloaded('relatorio_completo_nredutech.xlsx', function (AllReportsExport $export) {
            return $export->escolaId === $this->diretorA->id_escola;
        });
    }
}