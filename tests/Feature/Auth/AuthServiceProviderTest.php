<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\RecursoDidatico;
use App\Models\OfertaComponente;
use App\Models\Agendamento;
use Illuminate\Support\Facades\Gate;

class AuthServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
    protected $agendamentoProfessor;
    protected $agendamentoOutro;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $outraEscola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'id_escola' => null]);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $escola->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $escola->id_escola]);
        $professorOutraEscola = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $outraEscola->id_escola]);

        $turma = Turma::factory()->create(['id_escola' => $escola->id_escola]);
        $componente = ComponenteCurricular::factory()->create();
        $recurso = RecursoDidatico::factory()->create();

        $ofertaProfessor = OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_professor' => $this->professor->id_usuario,
            'id_componente' => $componente->id_componente
        ]);
        
        $ofertaOutro = OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_professor' => $professorOutraEscola->id_usuario,
            'id_componente' => $componente->id_componente
        ]);

        $this->agendamentoProfessor = Agendamento::factory()->create(['id_oferta' => $ofertaProfessor->id_oferta, 'id_recurso' => $recurso->id_recurso]);
        $this->agendamentoOutro = Agendamento::factory()->create(['id_oferta' => $ofertaOutro->id_oferta, 'id_recurso' => $recurso->id_recurso]);
    }

    /** @test */
    public function gate_administrador_funciona()
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('administrador'));
        $this->assertFalse(Gate::forUser($this->diretor)->allows('administrador'));
        $this->assertFalse(Gate::forUser($this->professor)->allows('administrador'));
    }

    /** @test */
    public function gate_cancelar_agendamento_permite_admin()
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('cancelar-agendamento', $this->agendamentoProfessor));
        $this->assertTrue(Gate::forUser($this->admin)->allows('cancelar-agendamento', $this->agendamentoOutro));
    }

    /** @test */
    public function gate_cancelar_agendamento_permite_professor_criador()
    {
        $this->assertTrue(Gate::forUser($this->professor)->allows('cancelar-agendamento', $this->agendamentoProfessor));
    }

    /** @test */
    public function gate_cancelar_agendamento_bloqueia_professor_nao_criador()
    {
        $this->assertFalse(Gate::forUser($this->professor)->allows('cancelar-agendamento', $this->agendamentoOutro));
    }

    /** @test */
    public function gate_cancelar_agendamento_permite_diretor_da_escola()
    {
        $this->assertTrue(Gate::forUser($this->diretor)->allows('cancelar-agendamento', $this->agendamentoProfessor));
        $this->assertTrue(Gate::forUser($this->diretor)->allows('cancelar-agendamento', $this->agendamentoOutro));
    }
}