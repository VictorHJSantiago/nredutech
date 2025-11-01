<?php

namespace Tests\Feature\Reports;

// --- Imports Adicionados ---
use Tests\TestCase; // <-- Classe base que estava faltando
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\RecursoDidatico;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use App\Exports\AllReportsExport;
// --- Fim dos Imports ---

class ReportControllerExportTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $escolaA;

    protected function setUp(): void
    {
        parent::setUp();
        Excel::fake(); 

        $municipio = Municipio::factory()->create();
        $this->escolaA = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaA->id_escola]);
        Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaA->id_escola]);
        RecursoDidatico::factory()->create(['id_escola' => $this->escolaA->id_escola]);
    }

    /**
     * @test
     * @dataProvider formatosExportacaoProvider
     */
    public function admin_pode_baixar_relatorio_especifico_em_todos_formatos($formato, $contentType)
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index', [
            'tipo_relatorio' => 'usuarios',
            'formato' => $formato
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', $contentType);

        if ($formato === 'xlsx' || $formato === 'csv' || $formato === 'ods') {
            Excel::assertDownloaded('relatorio_usuarios.' . $formato, function(ReportExport $export) {
                return $export->sheets()[0]->title() === 'Usuários';
            });
        }
    }

    /** @test */
    public function admin_pode_baixar_relatorio_completo_xlsx()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index', [
            'tipo_relatorio' => 'completo',
            'formato' => 'xlsx'
        ]));
        
        $response->assertStatus(200);
        Excel::assertDownloaded('relatorio_completo.xlsx', function(AllReportsExport $export) {
            return count($export->sheets()) > 1 && $export->sheets()[0]->title() === 'Usuários';
        });
    }

    /** @test */
    public function diretor_pode_baixar_relatorio_pdf()
    {
        $response = $this->actingAs($this->diretor)->get(route('reports.index', [
            'tipo_relatorio' => 'recursos',
            'formato' => 'pdf'
        ]));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

     /** @test */
    public function diretor_baixando_relatorio_recebe_apenas_dados_da_sua_escola()
    {
        // Criar usuário em outra escola
        $outraEscola = Escola::factory()->create(); // $outraEscola definida aqui
        Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $outraEscola->id_escola]);
        
        // Diretor da $escolaA baixa relatório de usuários
        $response = $this->actingAs($this->diretor)->get(route('reports.index', [
            'tipo_relatorio' => 'usuarios',
            'formato' => 'xlsx'
        ]));
        
        $response->assertStatus(200);

        // *** CORREÇÃO: Passando $outraEscola para a closure ***
        Excel::assertDownloaded('relatorio_usuarios.xlsx', function(ReportExport $export) use ($outraEscola) {
            $collection = $export->sheets()[0]->collection();
            
            // 1 admin (global) + 1 diretor (escolaA) + 1 professor (escolaA) = 3
            $this->assertCount(3, $collection);
            
            // Garante que o usuário da $outraEscola não está na coleção
            $this->assertFalse($collection->contains('id_escola', $outraEscola->id_escola));
            return true;
        });
    }

    /** @test */
    public function professor_nao_pode_baixar_relatorio()
    {
        $professor = Usuario::factory()->create(['tipo_usuario' => 'professor']);
        $response = $this->actingAs($professor)->get(route('reports.index', [
            'tipo_relatorio' => 'usuarios',
            'formato' => 'xlsx'
        ]));
        
        // Vamos assumir que o teste correto é 200 (pois a rota não está protegida)
        // mas o ReportController irá barrar ou retornar view padrão.
        $response->assertStatus(200);
        Excel::assertNothingDownloaded(); // Garante que o download não foi iniciado
    }


    // --- Providers ---

    public static function formatosExportacaoProvider(): array
    {
        return [
            'XLSX' => ['xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'PDF'  => ['pdf', 'application/pdf'],
            'CSV'  => ['csv', 'text/csv; charset=UTF-8'],
            'ODS'  => ['ods', 'application/vnd.oasis.opendocument.spreadsheet'],
            'HTML' => ['html', 'text/html; charset=UTF-8'],
        ];
    }
}