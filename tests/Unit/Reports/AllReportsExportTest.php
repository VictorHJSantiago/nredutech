<?php

namespace Tests\Unit\Reports; // Namespace correto

use Tests\TestCase; // Classe base
use App\Exports\AllReportsExport;
use App\Exports\SingleReportSheet;
use App\Models\Usuario;
use App\Models\RecursoDidatico;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AllReportsExportTest extends TestCase
{
    use RefreshDatabase; // Necessário para criar dados simulados

    /** @test */
    public function sheets_cria_aba_unica_para_relatorio_de_usuarios()
    {
        $filters = ['tipo_relatorio' => 'usuarios'];
        $export = new AllReportsExport($filters);

        // Injeta dados simulados (o construtor real buscaria no BD)
        $export->users = Usuario::factory(2)->make();

        $this->assertInstanceOf(WithMultipleSheets::class, $export);
        $sheets = $export->sheets();
        
        $this->assertCount(1, $sheets);
        $this->assertInstanceOf(SingleReportSheet::class, $sheets[0]);
        $this->assertEquals('Usuários', $sheets[0]->title());
    }

    /** @test */
    public function sheets_cria_aba_unica_para_relatorio_de_recursos()
    {
        $filters = ['tipo_relatorio' => 'recursos'];
        $export = new AllReportsExport($filters);
        $export->resources = RecursoDidatico::factory(3)->make();

        $sheets = $export->sheets();
        
        $this->assertCount(1, $sheets);
        $this->assertInstanceOf(SingleReportSheet::class, $sheets[0]);
        $this->assertEquals('Recursos Didáticos', $sheets[0]->title());
    }

    /** @test */
    public function sheets_cria_multiplas_abas_para_relatorio_completo()
    {
        $filters = ['tipo_relatorio' => 'completo'];
        $export = new AllReportsExport($filters);

        // Injeta dados simulados em todas as propriedades
        $export->users = Usuario::factory(2)->make();
        $export->schools = collect([]); // Simula
        $export->classes = collect([]); // Simula
        $export->resources = RecursoDidatico::factory(3)->make();
        $export->appointments = collect([]); // Simula

        $sheets = $export->sheets();
        
        $this->assertCount(5, $sheets); // Usuários, Escolas, Turmas, Recursos, Agendamentos
        $this->assertEquals('Usuários', $sheets[0]->title());
        $this->assertEquals('Escolas', $sheets[1]->title());
        $this->assertEquals('Turmas', $sheets[2]->title());
        $this->assertEquals('Recursos Didáticos', $sheets[3]->title());
        $this->assertEquals('Agendamentos', $sheets[4]->title());
    }
}