<?php

namespace Tests\Unit\Reports;

use Tests\TestCase;
use App\Exports\SingleReportSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use PHPUnit\Framework\Attributes\Test;

class SingleReportSheetTest extends TestCase
{
    private Collection $data;
    private string $reportType;
    private string $title;
    private string $escolaNome;
    private string $periodo;
    private array $headings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = collect([
            ['nome' => 'Recurso A', 'tipo' => 'Tipo 1', 'id_escola' => 'Escola X', 'agendamentos_count' => 15, 'total_hours' => 25.5],
            ['nome' => 'Recurso B', 'tipo' => 'Tipo 2', 'id_escola' => 'Escola Y', 'agendamentos_count' => 8, 'total_hours' => 12.0]
        ]);
        $this->reportType = 'usage_by_resource';
        $this->title = 'Uso por Recurso';
        $this->escolaNome = 'Escola Teste';
        $this->periodo = '01/01/2025 - 31/01/2025';
        
        $this->headings = [
            'usage_by_resource' => ['Recurso', 'Tipo', 'Escola', 'Agendamentos', 'Horas'],
            'usage_by_school' => ['Escola', 'Agendamentos', 'Horas', 'Recursos Usados', 'Professores Ativos'],
            'usage_by_course' => ['Componente', 'Turma', 'Professor', 'Agendamentos', 'Horas'],
            'usage_by_user' => ['Professor', 'Escola', 'Agendamentos', 'Horas', 'Recursos Usados'],
            'availability_by_resource' => ['Recurso', 'Tipo', 'Escola', 'Status', 'Total Agendado (h)', 'Total Disponível (h)'],
        ];
    }

    private function createSheet(string $type): SingleReportSheet
    {
        return new SingleReportSheet(
            $this->reportType,
            $this->data,
            $this->headings[$type]
        );
    }

    #[Test]
    public function planilha_implementa_interfaces_corretas()
    {
        $sheet = $this->createSheet('usage_by_resource');
        $this->assertInstanceOf(WithTitle::class, $sheet);
        $this->assertFalse(in_array(FromView::class, class_implements($sheet)));
    }

    #[Test]
    public function metodo_view_retorna_view_correta_e_dados()
    {
        $sheet = $this->createSheet('usage_by_resource');
        $this->assertFalse(method_exists($sheet, 'view'));
    }

    #[Test]
    public function metodo_titulo_retorna_titulo_correto()
    {
        $sheet = $this->createSheet('usage_by_resource');
        $this->assertEquals($this->reportType, $sheet->title());
    }

    #[Test]
    public function metodo_cabecalhos_retorna_cabecalhos_corretos_para_cada_tipo()
    {
        $sheet1 = $this->createSheet('usage_by_resource');
        $this->assertEquals($this->headings['usage_by_resource'], $sheet1->headings());

        $sheet2 = $this->createSheet('usage_by_school');
        $this->assertEquals($this->headings['usage_by_school'], $sheet2->headings());
    }
}