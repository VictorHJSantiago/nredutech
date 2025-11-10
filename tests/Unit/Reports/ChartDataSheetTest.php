<?php

namespace Tests\Unit\Reports;

use Tests\TestCase;
use App\Exports\ChartDataSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PHPUnit\Framework\Attributes\Test;

class ChartDataSheetTest extends TestCase
{
    #[Test]
    public function planilha_implementa_interfaces_corretas()
    {
        $sheet = new ChartDataSheet([]);
        $this->assertInstanceOf(FromArray::class, $sheet);
        $this->assertInstanceOf(WithHeadings::class, $sheet);
        $this->assertInstanceOf(WithTitle::class, $sheet);
    }

    #[Test]
    public function metodo_array_formata_dados_corretamente()
    {
        $chartData = [
            'recursosPorStatus' => collect([
                ['label' => 'Funcionando', 'value' => 10],
                ['label' => 'Quebrado', 'value' => 5]
            ]),
            'usuariosPorTipo' => collect([
                ['label' => 'Professor', 'value' => 20]
            ]),
            'agendamentosPorMes' => collect([
                ['label' => 'Jan', 'value' => 100],
                ['label' => 'Fev', 'value' => 150]
            ])
        ];
        
        $sheet = new ChartDataSheet($chartData);
        $array = $sheet->array();

        $this->assertCount(5, $array);
        $this->assertEquals(['Recursos por Status', 'Funcionando', 10], $array[0]);
        $this->assertEquals(['Recursos por Status', 'Quebrado', 5], $array[1]);
        $this->assertEquals(['Usuários por Tipo', 'Professor', 20], $array[2]);
        $this->assertEquals(['Agendamentos por Mês', 'Jan', 100], $array[3]);
        $this->assertEquals(['Agendamentos por Mês', 'Fev', 150], $array[4]);
    }

    #[Test]
    public function metodo_cabecalhos_retorna_cabecalhos_corretos()
    {
        $chartData = [
            'recursosPorStatus' => collect([]),
            'usuariosPorTipo' => collect([])
        ];
        
        $sheet = new ChartDataSheet($chartData);
        $headings = $sheet->headings();
        
        $this->assertEquals(['Indicador', 'Categoria', 'Valor'], $headings);
    }

    #[Test]
    public function metodo_titulo_retorna_titulo_correto()
    {
        $sheet = new ChartDataSheet([]);
        $this->assertEquals('Dados dos Gráficos', $sheet->title());
    }
}