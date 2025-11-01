<?php

namespace Tests\Feature\Reports; // Namespace correto

use Tests\TestCase; // Classe base
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\RecursoDidatico;
use App\Models\Turma;
use App\Models\Agendamento;
use App\Models\OfertaComponente;
use App\Models\ComponenteCurricular;

class ReportControllerViewTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretorEscolaA;
    protected $escolaA;
    protected $escolaB;
    protected $professorA; // Escola A
    protected $professorB; // Escola B
    protected $recursoA; // Escola A
    protected $recursoB; // Escola B
    protected $recursoGlobal; // Global
    protected $agendamentoA; // Escola A
    protected $agendamentoB; // Escola B

    protected function setUp(): void
    {
        parent::setUp();
        // Configuração complexa de dados
        $municipioA = Municipio::factory()->create(['nome' => 'Município A']);
        $municipioB = Municipio::factory()->create(['nome' => 'Município B']);
        
        $this->escolaA = Escola::factory()->create(['id_municipio' => $municipioA->id_municipio, 'nome' => 'Escola A', 'nivel_ensino' => 'medio', 'localizacao' => 'urbana']);
        $this->escolaB = Escola::factory()->create(['id_municipio' => $municipioB->id_municipio, 'nome' => 'Escola B', 'nivel_ensino' => 'fundamental_2', 'localizacao' => 'rural']);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'nome_completo' => 'Admin User']);
        $this->diretorEscolaA = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaA->id_escola, 'nome_completo' => 'Diretor A']);
        $this->professorA = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaA->id_escola, 'nome_completo' => 'Professor A']);
        $this->professorB = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaB->id_escola, 'nome_completo' => 'Professor B']);

        $this->recursoA = RecursoDidatico::factory()->create(['id_escola' => $this->escolaA->id_escola, 'nome' => 'Recurso A', 'status' => 'funcionando']);
        $this->recursoB = RecursoDidatico::factory()->create(['id_escola' => $this->escolaB->id_escola, 'nome' => 'Recurso B', 'status' => 'em_manutencao']);
        $this->recursoGlobal = RecursoDidatico::factory()->create(['id_escola' => null, 'nome' => 'Recurso Global', 'status' => 'funcionando']);

        // Agendamentos
        $turmaA = Turma::factory()->create(['id_escola' => $this->escolaA->id_escola]);
        $turmaB = Turma::factory()->create(['id_escola' => $this->escolaB->id_escola]);
        $componente = ComponenteCurricular::factory()->create();
        $ofertaA = OfertaComponente::factory()->create(['id_turma' => $turmaA->id_turma, 'id_professor' => $this->professorA->id_usuario, 'id_componente' => $componente->id_componente]);
        $ofertaB = OfertaComponente::factory()->create(['id_turma' => $turmaB->id_turma, 'id_professor' => $this->professorB->id_usuario, 'id_componente' => $componente->id_componente]);
        
        $this->agendamentoA = Agendamento::factory()->create(['id_oferta' => $ofertaA->id_oferta, 'id_recurso' => $this->recursoA->id_recurso]);
        $this->agendamentoB = Agendamento::factory()->create(['id_oferta' => $ofertaB->id_oferta, 'id_recurso' => $this->recursoB->id_recurso]);
    }

    /** @test */
    public function admin_ve_filtros_de_municipio_e_instituicao_na_view()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertSee('name="id_municipio"'); // Verifica se o seletor existe
        $response->assertSee('name="id_escola"');
    }

    /** @test */
    public function diretor_nao_ve_filtros_de_municipio_e_instituicao_na_view()
    {
        $response = $this->actingAs($this->diretorEscolaA)->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertDontSee('name="id_municipio"');
        $response->assertDontSee('name="id_escola"');
    }

    /** @test */
    public function admin_gera_relatorio_de_usuarios_e_ve_todos()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index', ['tipo_relatorio' => 'usuarios']));
        $response->assertStatus(200);
        $response->assertViewHas('results');
        $response->assertSee('Professor A');
        $response->assertSee('Professor B');
        $response->assertSee('Admin User');
        $response->assertSee('Diretor A');
    }

    /** @test */
    public function diretor_gera_relatorio_de_usuarios_e_ve_apenas_sua_escola_e_admins()
    {
        $response = $this->actingAs($this->diretorEscolaA)->get(route('reports.index', ['tipo_relatorio' => 'usuarios']));
        $response->assertStatus(200);
        $response->assertViewHas('results');
        $response->assertSee('Professor A'); // Da sua escola
        $response->assertDontSee('Professor B'); // De outra escola
        $response->assertSee('Admin User'); // Admin é global
        $response->assertSee('Diretor A'); // Ele mesmo
    }

    /** @test */
    public function admin_gera_relatorio_de_recursos_e_ve_todos()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index', ['tipo_relatorio' => 'recursos']));
        $response->assertStatus(200);
        $response->assertSee('Recurso A');
        $response->assertSee('Recurso B');
        $response->assertSee('Recurso Global');
    }

     /** @test */
    public function diretor_gera_relatorio_de_recursos_e_ve_apenas_sua_escola_e_globais()
    {
        $response = $this->actingAs($this->diretorEscolaA)->get(route('reports.index', ['tipo_relatorio' => 'recursos']));
        $response->assertStatus(200);
        $response->assertSee('Recurso A'); // Da sua escola
        $response->assertDontSee('Recurso B'); // De outra escola
        $response->assertSee('Recurso Global'); // Global
    }

    /** @test */
    public function admin_gera_relatorio_de_agendamentos_e_ve_todos()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index', ['tipo_relatorio' => 'agendamentos']));
        $response->assertStatus(200);
        $response->assertSee($this->agendamentoA->recurso->nome); // Agendamento A
        $response->assertSee($this->agendamentoB->recurso->nome); // Agendamento B
    }

    /** @test */
    public function diretor_gera_relatorio_de_agendamentos_e_ve_apenas_sua_escola()
    {
        $response = $this->actingAs($this->diretorEscolaA)->get(route('reports.index', ['tipo_relatorio' => 'agendamentos']));
        $response->assertStatus(200);
        $response->assertSee($this->agendamentoA->recurso->nome); // Da sua escola
        $response->assertDontSee($this->agendamentoB->recurso->nome); // De outra escola
    }

    /** @test */
    public function admin_pode_filtrar_relatorio_de_usuarios_por_escola()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index', [
            'tipo_relatorio' => 'usuarios',
            'id_escola' => $this->escolaB->id_escola, // Filtro de escola B
        ]));
        $response->assertStatus(200);
        $response->assertSee('Professor B');
        $response->assertDontSee('Professor A');
        $response->assertDontSee('Diretor A');
        // Admins são globais, então podem aparecer dependendo da lógica do 'buildQuery'
        // $response->assertDontSee('Admin User'); // Descomente se admins devem ser filtrados
    }

    /** @test */
    public function admin_pode_filtrar_relatorio_de_recursos_por_status_e_localizacao()
    {
        // escolaA = urbana, escolaB = rural
        // recursoA (EscolaA) = funcionando
        // recursoB (EscolaB) = em_manutencao
        // recursoGlobal = funcionando
        
        $response = $this->actingAs($this->admin)->get(route('reports.index', [
            'tipo_relatorio' => 'recursos',
            'localizacao' => 'urbana', // Filtro
            'status_recurso' => 'funcionando' // Filtro
        ]));
        $response->assertStatus(200);
        $response->assertSee('Recurso A'); // urbana e funcionando
        $response->assertSee('Recurso Global'); // global (sem localização) e funcionando
        $response->assertDontSee('Recurso B'); // rural e em_manutencao
    }

    /** @test */
    public function gera_dados_corretos_para_graficos_para_admin()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index', ['tipo_relatorio' => 'completo']));
        $response->assertStatus(200);
        $response->assertViewHas('chartsData');

        $chartsData = $response->viewData('chartsData');

        // Testa Recursos por Status (Total: 3)
        $this->assertArrayHasKey('resourcesByStatus', $chartsData);
        $this->assertEquals(2, $chartsData['resourcesByStatus']['funcionando']); // A e Global
        $this->assertEquals(1, $chartsData['resourcesByStatus']['em_manutencao']); // B
        $this->assertEquals(0, $chartsData['resourcesByStatus']['quebrado']);

        // Testa Usuários por Tipo (Total: 4)
        $this->assertArrayHasKey('usersByType', $chartsData);
        $this->assertEquals(1, $chartsData['usersByType']['administrador']);
        $this->assertEquals(1, $chartsData['usersByType']['diretor']);
        $this->assertEquals(2, $chartsData['usersByType']['professor']);
    }

    /** @test */
    public function gera_dados_corretos_para_graficos_para_diretor()
    {
        $response = $this->actingAs($this->diretorEscolaA)->get(route('reports.index', ['tipo_relatorio' => 'completo']));
        $response->assertStatus(200);
        $response->assertViewHas('chartsData');

        $chartsData = $response->viewData('chartsData');

        // Testa Recursos por Status (Visíveis: A e Global)
        $this->assertArrayHasKey('resourcesByStatus', $chartsData);
        $this->assertEquals(2, $chartsData['resourcesByStatus']['funcionando']); // A e Global
        $this->assertEquals(0, $chartsData['resourcesByStatus']['em_manutencao']); // B (outra escola) não aparece
        $this->assertEquals(0, $chartsData['resourcesByStatus']['quebrado']);

        // Testa Usuários por Tipo (Visíveis: Admin, Diretor A, Professor A)
        $this->assertArrayHasKey('usersByType', $chartsData);
        $this->assertEquals(1, $chartsData['usersByType']['administrador']);
        $this->assertEquals(1, $chartsData['usersByType']['diretor']);
        $this->assertEquals(1, $chartsData['usersByType']['professor']); // Só o Professor A
    }
}