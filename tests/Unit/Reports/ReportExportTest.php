<?php

namespace Tests\Unit\Reports; 

use Tests\TestCase;
use App\Exports\ReportExport;
use App\Exports\SingleReportSheet; 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use App\Models\Usuario; 

class ReportExportTest extends TestCase
{
    /** @test */
    public function exportacao_de_usuarios_retorna_colecao_correta()
    {
        $dadosSimulados = collect([
            Usuario::factory()->make(['nome_completo' => 'Alice', 'email' => 'alice@test.com', 'tipo_usuario' => 'professor']),
            Usuario::factory()->make(['nome_completo' => 'Bob', 'email' => 'bob@test.com', 'tipo_usuario' => 'diretor']),
        ]);
        $titulo = 'Relatório de Usuários';
        $headings = ['Nome Completo', 'Email', 'Tipo']; 
        $sheet = new SingleReportSheet($dadosSimulados, $titulo, $headings); 
        $this->assertInstanceOf(FromCollection::class, $sheet);
        $exportedCollection = $sheet->collection();
        $this->assertInstanceOf(Collection::class, $exportedCollection);
        $this->assertCount(2, $exportedCollection); 

        $this->assertInstanceOf(WithHeadings::class, $sheet);
        $exportedHeadings = $sheet->headings();
        $this->assertEquals($headings, $exportedHeadings);

        if ($sheet instanceof WithMapping) {
            $primeiroUsuarioMapeado = $sheet->map($dadosSimulados->first());
            $this->assertEquals([
                'Alice', 
                'alice@test.com', 
                'professor' 
            ], $primeiroUsuarioMapeado);
        }
    }

    /** @test */
    public function exportacao_sem_dados_retorna_colecao_vazia_com_cabecalhos()
    {
        $dadosVazios = collect([]);
        $titulo = 'Relatório Vazio';
        $headings = ['Coluna A', 'Coluna B'];

        $sheet = new SingleReportSheet($dadosVazios, $titulo, $headings);

        $exportedCollection = $sheet->collection();
        $this->assertCount(0, $exportedCollection);

        $exportedHeadings = $sheet->headings();
        $this->assertEquals($headings, $exportedHeadings);
    }

    // Adicionar testes para AllReportsExport se ele tiver lógica própria (ex: definir múltiplas sheets)
    // Exemplo:
    /*
    use App\Exports\AllReportsExport;

    /** @test * /
    public function all_reports_export_define_multiplas_sheets_corretamente()
    {
        $dadosUsuarios = collect([...]);
        $dadosRecursos = collect([...]);
        $filters = ['tipo_relatorio' => 'completo']; // Filtros simulados

        $export = new AllReportsExport($filters); // Supondo que AllReportsExport busca os dados

        // Mockar as buscas de dados dentro de AllReportsExport se necessário

        $sheets = $export->sheets();

        $this->assertIsArray($sheets);
        $this->assertCount(2, $sheets); // Supondo que gera 2 abas (usuários, recursos)
        $this->assertInstanceOf(SingleReportSheet::class, $sheets[0]);
        $this->assertInstanceOf(SingleReportSheet::class, $sheets[1]);
        // Verificar títulos das abas se a classe SingleReportSheet implementar WithTitle
        // $this->assertEquals('Usuarios', $sheets[0]->title());
        // $this->assertEquals('Recursos', $sheets[1]->title());
    }
    */
}