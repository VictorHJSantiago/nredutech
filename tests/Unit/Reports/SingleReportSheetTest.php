<?php

namespace Tests\Unit\Reports; // Namespace correto

use Tests\TestCase; // Classe base
use App\Exports\SingleReportSheet;
use App\Models\Usuario;
use App\Models\RecursoDidatico;
use App\Models\Escola;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class SingleReportSheetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function sheet_implementa_interfaces_necessarias()
    {
        $sheet = new SingleReportSheet(collect([]), 'Teste', []);
        $this->assertInstanceOf(WithHeadings::class, $sheet);
        $this->assertInstanceOf(WithTitle::class, $sheet);
        $this->assertInstanceOf(WithMapping::class, $sheet); // Importante para formatação
    }

    /** @test */
    public function retorna_titulo_e_cabecalhos_corretamente()
    {
        $titulo = 'Aba de Teste';
        $headings = ['ID', 'Nome', 'Status'];
        $sheet = new SingleReportSheet(collect([]), $titulo, $headings);

        $this->assertEquals($titulo, $sheet->title());
        $this->assertEquals($headings, $sheet->headings());
    }

    /** @test */
    public function mapeia_corretamente_relatorio_de_usuarios()
    {
        $escola = Escola::factory()->make(['nome' => 'Escola Modelo']);
        $usuario = Usuario::factory()->make([
            'id_usuario' => 10,
            'nome_completo' => 'Ana Silva',
            'username' => 'ana.silva',
            'email' => 'ana@email.com',
            'tipo_usuario' => 'professor',
            'status_aprovacao' => 'ativo',
            'created_at' => Carbon::parse('2024-01-01 10:30:00')
        ]);
        $usuario->setRelation('escola', $escola);

        $headings = ['ID', 'Nome Completo', 'Username', 'Email', 'Tipo', 'Escola', 'Status', 'Data Cadastro'];
        $sheet = new SingleReportSheet(collect([$usuario]), 'Usuários', $headings);

        $mappedData = $sheet->map($usuario);

        $this->assertEquals([
            10,
            'Ana Silva',
            'ana.silva',
            'ana@email.com',
            'professor',
            'Escola Modelo', // Nome da Relação
            'ativo',
            '01/01/2024 10:30' // Formato de Data
        ], $mappedData);
    }

    /** @test */
    public function mapeia_corretamente_usuario_admin_sem_escola()
    {
        $admin = Usuario::factory()->make(['id_usuario' => 1, 'tipo_usuario' => 'administrador', 'id_escola' => null]);
        $admin->setRelation('escola', null); // Relação nula

        $headings = ['ID', 'Nome Completo', 'Username', 'Email', 'Tipo', 'Escola', 'Status', 'Data Cadastro'];
        $sheet = new SingleReportSheet(collect([$admin]), 'Usuários', $headings);

        $mappedData = $sheet->map($admin);
        $this->assertEquals('N/A', $mappedData[5]); // Índice 5 (Escola) deve ser 'N/A'
    }

    /** @test */
    public function mapeia_corretamente_relatorio_de_recursos()
    {
        $recurso = RecursoDidatico::factory()->make([
            'id_recurso' => 5,
            'nome' => 'Projetor Sala 1',
            'tipo' => 'didatico',
            'marca' => 'Epson',
            'numero_serie' => 'SN123',
            'quantidade' => 1,
            'status' => 'funcionando',
            'data_aquisicao' => Carbon::parse('2023-05-10')
        ]);
        $recurso->setRelation('escola', null); // Global

        $headings = ['ID', 'Nome', 'Tipo', 'Marca', 'Nº Série', 'Qtd', 'Status', 'Escola', 'Data Aquisição'];
        $sheet = new SingleReportSheet(collect([$recurso]), 'Recursos', $headings);

        $mappedData = $sheet->map($recurso);

        $this->assertEquals([
            5,
            'Projetor Sala 1',
            'didatico',
            'Epson',
            'SN123',
            1,
            'funcionando',
            'Global', // Escola Global
            '10/05/2023' // Formato de Data
        ], $mappedData);
    }
}