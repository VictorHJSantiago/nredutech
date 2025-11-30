<?php

namespace Tests\Feature\Dashboard;

use App\Models\Agendamento;
use App\Models\ComponenteCurricular;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\OfertaComponente;
use App\Models\RecursoDidatico;
use App\Models\Turma;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private $escola;
    private $municipio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->municipio = Municipio::create(['nome' => 'Curitiba']);

        $this->escola = Escola::create([
            'nome' => 'Escola Teste',
            'nivel_ensino' => 'Médio',
            'tipo' => 'Estadual',
            'id_municipio' => $this->municipio->id_municipio
        ]);
    }

    public function test_painel_admin_mostra_kpis_do_sistema()
    {
        $admin = Usuario::factory()->create([
            'tipo_usuario' => 'administrador',
            'status_aprovacao' => 'ativo'
        ]);

        Escola::create([
            'nome' => 'Escola 2',
            'nivel_ensino' => 'Fundamental',
            'tipo' => 'Municipal',
            'id_municipio' => $this->municipio->id_municipio
        ]);

        $recurso = RecursoDidatico::create([
            'nome' => 'Projetor',
            'status' => 'funcionando',
            'quantidade' => 1,
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $admin->id_usuario
        ]);
        
        RecursoDidatico::create([
            'nome' => 'Quebrado',
            'status' => 'em_manutencao',
            'quantidade' => 1,
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $admin->id_usuario
        ]);

        $turma = Turma::create([
            'serie' => '1º Ano',
            'ano_letivo' => 2025,
            'turno' => 'manha',
            'nivel_escolaridade' => 'medio',
            'id_escola' => $this->escola->id_escola
        ]);

        $componente = ComponenteCurricular::create([
            'nome' => 'Matemática',
            'carga_horaria' => 60,
            'status' => 'aprovado'
        ]);

        $professor = Usuario::factory()->create([
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escola->id_escola,
            'status_aprovacao' => 'ativo'
        ]);

        $oferta = OfertaComponente::create([
            'id_turma' => $turma->id_turma,
            'id_professor' => $professor->id_usuario,
            'id_componente' => $componente->id_componente
        ]);

        Agendamento::create([
            'data_hora_inicio' => now(),
            'data_hora_fim' => now()->addHour(),
            'status' => 'agendado',
            'id_oferta' => $oferta->id_oferta,
            'id_recurso' => $recurso->id_recurso
        ]);

        $response = $this->actingAs($admin)->get(route('index'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response->viewData('stats');
        
        $this->assertEquals(2, $stats['total_escolas']);
        $this->assertEquals(Usuario::count(), $stats['total_usuarios']);
        $this->assertEquals(1, $stats['recursos_disponiveis']);
        $this->assertEquals(1, $stats['agendamentos_hoje']);
    }

    public function test_painel_diretor_mostra_kpis_especificos_da_escola()
    {
        $diretor = Usuario::factory()->create([
            'tipo_usuario' => 'diretor',
            'id_escola' => $this->escola->id_escola,
            'status_aprovacao' => 'ativo'
        ]);

        $recurso = RecursoDidatico::create([
            'nome' => 'Notebook',
            'status' => 'funcionando',
            'quantidade' => 10,
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $diretor->id_usuario
        ]);

        $turma = Turma::create([
            'serie' => '3º Ano',
            'ano_letivo' => 2025,
            'turno' => 'tarde',
            'nivel_escolaridade' => 'medio',
            'id_escola' => $this->escola->id_escola
        ]);

        $componente = ComponenteCurricular::create([
            'nome' => 'História',
            'carga_horaria' => 40,
            'status' => 'aprovado'
        ]);

        $professor = Usuario::factory()->create([
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escola->id_escola,
            'status_aprovacao' => 'ativo'
        ]);

        $oferta = OfertaComponente::create([
            'id_turma' => $turma->id_turma,
            'id_professor' => $professor->id_usuario,
            'id_componente' => $componente->id_componente
        ]);

        Agendamento::create([
            'data_hora_inicio' => now(),
            'data_hora_fim' => now()->addHour(),
            'status' => 'agendado',
            'id_oferta' => $oferta->id_oferta,
            'id_recurso' => $recurso->id_recurso
        ]);

        $outraEscola = Escola::create([
            'nome' => 'Escola Vizinha',
            'nivel_ensino' => 'Fundamental',
            'tipo' => 'Municipal',
            'id_municipio' => $this->municipio->id_municipio
        ]);

        $turmaOutra = Turma::create([
            'serie' => '5º Ano',
            'ano_letivo' => 2025,
            'turno' => 'manha',
            'nivel_escolaridade' => 'fundamental_1',
            'id_escola' => $outraEscola->id_escola
        ]);

        $profOutro = Usuario::factory()->create([
            'tipo_usuario' => 'professor',
            'id_escola' => $outraEscola->id_escola,
            'status_aprovacao' => 'ativo'
        ]);

        $ofertaOutra = OfertaComponente::create([
            'id_turma' => $turmaOutra->id_turma,
            'id_professor' => $profOutro->id_usuario,
            'id_componente' => $componente->id_componente
        ]);

        Agendamento::create([
            'data_hora_inicio' => now(),
            'data_hora_fim' => now()->addHour(),
            'status' => 'agendado',
            'id_oferta' => $ofertaOutra->id_oferta,
            'id_recurso' => $recurso->id_recurso
        ]);

        $response = $this->actingAs($diretor)->get(route('index'));

        $response->assertStatus(200);
        $stats = $response->viewData('stats');

        $this->assertEquals(1, $stats['total_escolas']);
        $this->assertEquals(Usuario::where('id_escola', $this->escola->id_escola)->count(), $stats['total_usuarios']);
        $this->assertEquals(1, $stats['agendamentos_hoje']);
    }

    public function test_painel_professor_mostra_kpis_pessoais()
    {
        $professor = Usuario::factory()->create([
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escola->id_escola,
            'status_aprovacao' => 'ativo'
        ]);

        RecursoDidatico::create([
            'nome' => 'Mapa',
            'status' => 'funcionando',
            'quantidade' => 1,
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $professor->id_usuario
        ]);

        $response = $this->actingAs($professor)->get(route('index'));

        $response->assertStatus(200);
        $stats = $response->viewData('stats');

        $this->assertEquals(1, $stats['total_escolas']);
        $this->assertEquals(Usuario::where('id_escola', $this->escola->id_escola)->count(), $stats['total_usuarios']);
    }
}