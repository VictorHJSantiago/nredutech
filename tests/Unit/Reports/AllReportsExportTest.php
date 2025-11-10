<?php

namespace Tests\Unit\Reports;

use Tests\TestCase;
use App\Exports\AllReportsExport;
use App\Exports\KpiSheet;
use App\Exports\ChartDataSheet;
use App\Exports\SingleReportSheet;
use PHPUnit\Framework\Attributes\Test;

class AllReportsExportTest extends TestCase
{
    private $reports;
    private $stats;
    private $chartData;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->reports = [
            'usage_by_resource' => [
                'title' => 'Uso por Recurso',
                'columns' => ['Recurso', 'Tipo', 'Agendamentos', 'Horas'],
                'data' => collect([
                    ['nome' => 'Projetor A', 'tipo' => 'Projetor', 'agendamentos_count' => 10, 'total_hours' => 25.5],
                ])
            ],
            'usage_by_school' => [
                'title' => 'Uso por Escola',
                'columns' => ['Escola', 'Agendamentos', 'Horas'],
                'data' => collect([
                    ['nome' => 'Escola A', 'agendamentos_count' => 50, 'total_hours' => 120],
                ])
            ]
        ];
        
        $this->stats = ['total_agendamentos' => 100, 'total_horas' => 250.5];
        $this->chartData = [
            'recursosPorStatus' => collect([['label' => 'Funcionando', 'value' => 10]]),
            'usuariosPorTipo' => collect([['label' => 'Professor', 'value' => 20]])
        ];
    }

    #[Test]
    public function construtor_define_propriedades()
    {
        $export = new AllReportsExport(
            $this->reports,
            $this->stats,
            $this->chartData
        );

        $this->assertInstanceOf(AllReportsExport::class, $export);
    }

    #[Test]
    public function metodo_planilhas_retorna_planilhas_corretas()
    {
        $export = new AllReportsExport(
            $this->reports,
            $this->stats,
            $this->chartData
        );
        
        $sheets = $export->sheets();

        $this->assertCount(4, $sheets);
        $this->assertInstanceOf(KpiSheet::class, $sheets[0]);
        $this->assertInstanceOf(ChartDataSheet::class, $sheets[1]);
        $this->assertInstanceOf(SingleReportSheet::class, $sheets[2]);
        $this->assertInstanceOf(SingleReportSheet::class, $sheets[3]);
    }
}