<?php

namespace Tests\Feature\CourseOffering;

use App\Models\ComponenteCurricular;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\OfertaComponente;
use App\Models\Turma;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CourseOfferingRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
    protected $outroProfessor;
    protected $escola;
    protected $outraEscola;
    protected $oferta;
    protected $ofertaOutraEscola;
    protected $ofertaOutroProfessor;
    protected $componente;

    protected function setUp(): void
    {
        parent::setUp();

        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $this->escola = Escola::create(['nome' => 'Escola Teste', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        $this->outraEscola = Escola::create(['nome' => 'Outra Escola', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola]);
        $this->outroProfessor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola]);

        $this->componente = ComponenteCurricular::factory()->create(['id_escola' => null, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão']);
        $turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        $turmaOutraEscola = Turma::factory()->create(['id_escola' => $this->outraEscola->id_escola]);

        $this->oferta = OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ]);

        $this->ofertaOutraEscola = OfertaComponente::factory()->create([
            'id_turma' => $turmaOutraEscola->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->admin->id_usuario,
        ]);

        $this->ofertaOutroProfessor = OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->outroProfessor->id_usuario,
        ]);
    }

    #[Test]
    public function convidado_e_redirecionado_de_todas_rotas_de_oferta_de_componente()
    {
        $this->get(route('ofertas.index'))->assertRedirect(route('login'));
        $this->post(route('ofertas.store'))->assertRedirect(route('login'));
        $this->get(route('ofertas.edit', $this->oferta))->assertRedirect(route('login'));
        $this->put(route('ofertas.update', $this->oferta))->assertRedirect(route('login'));
        $this->delete(route('ofertas.destroy', $this->oferta))->assertRedirect(route('login'));
    }

    #[Test]
    public function admin_pode_acessar_todas_rotas_de_oferta_de_componente()
    {
        $data = [
            'id_turma' => $this->oferta->id_turma,
            'id_componente' => $this->oferta->id_componente,
            'id_professor' => $this->admin->id_usuario,
        ];
        $this->actingAs($this->admin)->post(route('ofertas.store'), $data)->assertRedirect();
        $this->actingAs($this->admin)->delete(route('ofertas.destroy', $this->oferta))->assertRedirect();
    }

    #[Test]
    public function diretor_pode_acessar_todas_rotas_de_oferta_de_componente()
    {
        $data = [
            'id_turma' => $this->oferta->id_turma,
            'id_componente' => $this->oferta->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ];
        $this->actingAs($this->diretor)->post(route('ofertas.store'), $data)->assertRedirect();
        $this->actingAs($this->diretor)->delete(route('ofertas.destroy', $this->oferta))->assertRedirect();
    }

    #[Test]
    public function professor_pode_acessar_index_e_rotas_proprias()
    {
        $data = [
            'id_turma' => $this->oferta->id_turma,
            'id_componente' => $this->oferta->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ];
        $this->actingAs($this->professor)->post(route('ofertas.store'), $data)->assertRedirect();
        $this->actingAs($this->professor)->delete(route('ofertas.destroy', $this->oferta))->assertRedirect();
    }

    #[Test]
    public function professor_e_proibido_nas_rotas_de_outros_professores()
    {
        $this->actingAs($this->professor)->delete(route('ofertas.destroy', $this->ofertaOutroProfessor))->assertRedirect();
    }
}