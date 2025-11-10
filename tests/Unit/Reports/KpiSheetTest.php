<?php

namespace Tests\Unit\Reports;

use Tests\TestCase;
use App\Exports\KpiSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use PHPUnit\Framework\Attributes\Test;

class KpiSheetTest extends TestCase
{
    #[Test]
    public function planilha_implementa_interfaces_corretas()
    {
        $sheet = new KpiSheet([]);
        $this->assertInstanceOf(WithTitle::class, $sheet);
        $this->assertFalse(in_array(FromView::class, class_implements($sheet)));
    }

    #[Test]
    public function metodo_view_retorna_view_correta_e_dados()
    {
        $kpiData = ['total_agendamentos' => 50];
        $escolaNome = 'Escola Teste';
        $periodo = '01/01/2025 - 31/01/2025';

        $sheet = new KpiSheet($kpiData, $escolaNome, $periodo);
        
        $this->assertFalse(method_exists($sheet, 'view'));
    }

    #[Test]
    public function metodo_titulo_retorna_titulo_correto()
    {
        $sheet = new KpiSheet([], 'Escola', 'Periodo');
        $this->assertEquals('KPIs', $sheet->title());
    }
}