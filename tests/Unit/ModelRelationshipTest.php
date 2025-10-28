<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\Municipio;
use App\Models\ComponenteCurricular;
use App\Models\RecursoDidatico;
use App\Models\OfertaComponente;
use App\Models\Agendamento;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usuario_pertence_a_uma_escola()
    {
        $municipio = Municipio::factory()->create();
        $escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $usuario = Usuario::factory()->create(['id_escola' => $escola->id_escola]);

        $this->assertInstanceOf(Escola::class, $usuario->escola);
        $this->assertEquals($escola->id_escola, $usuario->escola->id_escola);
    }

    /** @test */
    public function escola_tem_muitos_usuarios_e_turmas()
    {
        $municipio = Municipio::factory()->create();
        $escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        Usuario::factory(3)->create(['id_escola' => $escola->id_escola]);
        Turma::factory(2)->create(['id_escola' => $escola->id_escola]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $escola->usuarios);
        $this->assertCount(3, $escola->usuarios);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $escola->turmas);
        $this->assertCount(2, $escola->turmas);
    }

    /** @test */
    public function agendamento_pertence_a_um_recurso_e_uma_oferta()
    {
        $municipio = Municipio::factory()->create();
        $escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $professor = Usuario::factory()->create(['id_escola' => $escola->id_escola, 'tipo_usuario' => 'professor']);
        $turma = Turma::factory()->create(['id_escola' => $escola->id_escola]);
        $componente = ComponenteCurricular::factory()->create(['id_escola' => null]);
        $recurso = RecursoDidatico::factory()->create(['id_escola' => null]);
        
        $oferta = OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_professor' => $professor->id_usuario,
            'id_componente' => $componente->id_componente
        ]);

        $agendamento = Agendamento::factory()->create([
            'id_recurso' => $recurso->id_recurso,
            'id_oferta' => $oferta->id_oferta
        ]);

        $this->assertInstanceOf(RecursoDidatico::class, $agendamento->recurso);
        $this->assertEquals($recurso->id_recurso, $agendamento->recurso->id_recurso);

        $this->assertInstanceOf(OfertaComponente::class, $agendamento->oferta);
        $this->assertEquals($oferta->id_oferta, $agendamento->oferta->id_oferta);
    }

    /** @test */
    public function recurso_didatico_pertence_a_um_criador_e_escola()
    {
        $municipio = Municipio::factory()->create();
        $escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $criador = Usuario::factory()->create(['id_escola' => $escola->id_escola]);

        $recurso = RecursoDidatico::factory()->create([
            'id_escola' => $escola->id_escola,
            'id_usuario_criador' => $criador->id_usuario
        ]);

        $this->assertInstanceOf(Escola::class, $recurso->escola);
        $this->assertEquals($escola->id_escola, $recurso->escola->id_escola);

        $this->assertInstanceOf(Usuario::class, $recurso->criador);
        $this->assertEquals($criador->id_usuario, $recurso->criador->id_usuario);
    }
}