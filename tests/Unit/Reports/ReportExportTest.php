<?php

namespace Tests\Unit\Reports;

use Tests\TestCase;
use App\Exports\ReportExport;
use App\Exports\SingleReportSheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PHPUnit\Framework\Attributes\Test;

class ReportExportTest extends TestCase
{
    private $data;
    private $headings;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->data = collect([
            (object)['col1' => 'Projetor A', 'col2' => 'Projetor', 'col3' => 10, 'col4' => 25.5],
        ]);
        $this->headings = ['col1' => 'Recurso', 'col2' => 'Tipo', 'col3' => 'Agendamentos', 'col4' => 'Horas'];
    }

    #[Test]
    public function construtor_define_propriedades()
    {
        $export = new ReportExport($this->data, $this->headings);
        
        $this->assertInstanceOf(ReportExport::class, $export);
        $this->assertFalse(in_array(FromArray::class, class_implements($export)));
        $this->assertInstanceOf(WithHeadings::class, $export);
    }

    #[Test]
    public function metodos_array_e_cabecalhos_retornam_dados_corretos()
    {
        $export = new ReportExport(
            $this->data,
            $this->headings
        );

        $this->assertFalse(method_exists($export, 'array'));
        $headingsData = $export->headings();
        
        $this->assertEquals(['Recurso', 'Tipo', 'Agendamentos', 'Horas'], $headingsData);
    }
}