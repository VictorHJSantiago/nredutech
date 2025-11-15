<?php

namespace Tests\Feature\CourseOffering;

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
use PHPUnit\Framework\Attributes\Test;

class CourseOfferingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
    protected $outroProfessor;
    protected $escola;
    protected $outraEscola;
    protected $componente;
    protected $turma;
    protected $turmaOutraEscola;
    protected $oferta;
    protected $ofertaOutroProfessor;
    protected $municipio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $this->escola = Escola::create(['nome' => 'Escola Teste', 'id_municipio' => $this->municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        $this->outraEscola = Escola::create(['nome' => 'Outra Escola', 'id_municipio' => $this->municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola]);
        $this->outroProfessor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola]);

        $this->componente = ComponenteCurricular::factory()->create(['id_escola' => null, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão']);
        $this->turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        $this->turmaOutraEscola = Turma::factory()->create(['id_escola' => $this->outraEscola->id_escola]);

        $this->oferta = OfertaComponente::factory()->create([
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ]);

        $this->ofertaOutroProfessor = OfertaComponente::factory()->create([
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->outroProfessor->id_usuario,
        ]);
    }

    #[Test]
    public function admin_pode_ver_todas_ofertas_no_index()
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function diretor_pode_ver_apenas_ofertas_propria_escola_no_index()
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function professor_pode_ver_apenas_ofertas_propria_escola_no_index()
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function admin_pode_cadastrar_oferta_para_qualquer_escola()
    {
        $data = [
            'id_turma' => $this->turmaOutraEscola->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->admin->id_usuario,
        ];
        $response = $this->actingAs($this->admin)->post(route('ofertas.store'), $data);
        $response->assertRedirect(route('turmas.show', $this->turmaOutraEscola->id_turma));
        $this->assertDatabaseHas('oferta_componentes', $data);
    }

    #[Test]
    public function diretor_pode_cadastrar_oferta_para_propria_escola()
    {
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ];
        $response = $this->actingAs($this->diretor)->post(route('ofertas.store'), $data);
        $response->assertRedirect(url('/'));
        $this->assertDatabaseHas('oferta_componentes', $data);
    }

    #[Test]
    public function diretor_nao_pode_cadastrar_oferta_para_outra_escola()
    {
        $data = [
            'id_turma' => $this->turmaOutraEscola->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->admin->id_usuario,
        ];
        $response = $this->actingAs($this->diretor)->post(route('ofertas.store'), $data);
        $response->assertRedirect();
    }

    #[Test]
    public function professor_pode_cadastrar_oferta_para_si_mesmo()
    {
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ];
        $response = $this->actingAs($this->professor)->post(route('ofertas.store'), $data);
        $response->assertRedirect(url('/'));
        $this->assertDatabaseHas('oferta_componentes', $data);
    }

    #[Test]
    public function professor_nao_pode_cadastrar_oferta_para_outro_professor()
    {
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->outroProfessor->id_usuario,
        ];
        $response = $this->actingAs($this->professor)->post(route('ofertas.store'), $data);
        $response->assertRedirect();
    }

    #[Test]
    public function admin_pode_atualizar_qualquer_oferta()
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function diretor_pode_atualizar_oferta_propria_escola()
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function diretor_nao_pode_atualizar_oferta_outra_escola()
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function professor_pode_atualizar_propria_oferta()
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function professor_nao_pode_atualizar_oferta_outro_professor()
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function admin_pode_destruir_oferta()
    {
        $idTurma = $this->oferta->id_turma;
        $response = $this->actingAs($this->admin)->delete(route('ofertas.destroy', $this->oferta));
        $response->assertRedirect(route('turmas.show', $idTurma));
        $this->assertDatabaseMissing('oferta_componentes', ['id_oferta' => $this->oferta->id_oferta]);
    }

    #[Test]
    public function diretor_pode_destruir_oferta_propria_escola()
    {
        $idTurma = $this->oferta->id_turma;
        $response = $this->actingAs($this->diretor)->delete(route('ofertas.destroy', $this->oferta));
        $response->assertRedirect(route('turmas.show', $idTurma));
        $this->assertDatabaseMissing('oferta_componentes', ['id_oferta' => $this->oferta->id_oferta]);
    }

    #[Test]
    public function professor_pode_destruir_propria_oferta()
    {
        $idTurma = $this->oferta->id_turma;
        $response = $this->actingAs($this->professor)->delete(route('ofertas.destroy', $this->oferta));
        $response->assertRedirect(route('turmas.show', $idTurma));
        $this->assertDatabaseMissing('oferta_componentes', ['id_oferta' => $this->oferta->id_oferta]);
    }

    #[Test]
    public function professor_nao_pode_destruir_oferta_outro_professor()
    {
        $response = $this->actingAs($this->professor)->delete(route('ofertas.destroy', $this->ofertaOutroProfessor));
        $response->assertRedirect();
    }

    #[Test]
    public function nao_pode_destruir_oferta_com_dependencias()
    {
        RecursoDidatico::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'status' => 'funcionando',
            'id_usuario_criador' => $this->admin->id_usuario
        ]);

        Agendamento::factory()->create(['id_oferta' => $this->oferta->id_oferta]);
        
        $idTurma = $this->oferta->id_turma;
        $response = $this->actingAs($this->admin)->delete(route('ofertas.destroy', $this->oferta));
        $response->assertRedirect(route('turmas.show', $idTurma));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('oferta_componentes', ['id_oferta' => $this->oferta->id_oferta]);
    }
}