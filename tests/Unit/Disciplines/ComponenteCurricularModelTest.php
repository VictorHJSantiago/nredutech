<?php

namespace Tests\Unit\Disciplines; 

use Tests\TestCase;
use App\Models\ComponenteCurricular;
use App\Models\Escola;
use App\Models\Usuario;
use App\Models\OfertaComponente;
use App\Models\Municipio; 
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComponenteCurricularModelTest extends TestCase
{
    use RefreshDatabase;

    protected $escola;
    protected $criador;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $this->escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $this->criador = Usuario::factory()->create(['id_escola' => $this->escola->id_escola]);
    }

    /** @test */
    public function componente_pode_pertencer_a_uma_escola()
    {
        $componente = ComponenteCurricular::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $this->criador->id_usuario
        ]);

        $this->assertInstanceOf(Escola::class, $componente->escola);
        $this->assertEquals($this->escola->id_escola, $componente->escola->id_escola);
    }

     /** @test */
    public function componente_pode_ser_global_sem_escola()
    {
        $componente = ComponenteCurricular::factory()->create([
            'id_escola' => null, 
            'id_usuario_criador' => $this->criador->id_usuario
        ]);

        $this->assertNull($componente->escola);
    }

    /** @test */
    public function componente_pertence_a_um_criador()
    {
        $componente = ComponenteCurricular::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $this->criador->id_usuario
        ]);

        $this->assertInstanceOf(Usuario::class, $componente->criador);
        $this->assertEquals($this->criador->id_usuario, $componente->criador->id_usuario);
    }

     /** @test */
    public function componente_pode_ter_muitas_ofertas()
    {
        $componente = ComponenteCurricular::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $this->criador->id_usuario
        ]);
        OfertaComponente::factory(3)->create(['id_componente' => $componente->id_componente]);
        $outroComponente = ComponenteCurricular::factory()->create();
        OfertaComponente::factory()->create(['id_componente' => $outroComponente->id_componente]);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $componente->ofertas);
        $this->assertCount(3, $componente->ofertas);
    }


    /** @test */
    public function atributos_fillable_estao_corretos()
    {
        $componente = new ComponenteCurricular();
        $expected = ['nome', 'descricao', 'carga_horaria', 'status', 'id_usuario_criador', 'id_escola'];
        $this->assertEqualsCanonicalizing($expected, $componente->getFillable());
    }

    /** @test */
    public function chave_primaria_e_id_componente()
    {
        $componente = new ComponenteCurricular();
        $this->assertEquals('id_componente', $componente->getKeyName());
    }

    /** @test */
    public function timestamps_sao_usados_por_padrao()
    {
        $componente = new ComponenteCurricular();
        $this->assertTrue($componente->usesTimestamps());
    }

    /*
    /** @test * /
    public function scope_aprovados_retorna_apenas_componentes_aprovados()
    {
        ComponenteCurricular::factory()->create(['status' => 'aprovado']);
        ComponenteCurricular::factory()->create(['status' => 'pendente']);
        ComponenteCurricular::factory()->create(['status' => 'aprovado']);
        ComponenteCurricular::factory()->create(['status' => 'reprovado']);

        $aprovados = ComponenteCurricular::aprovados()->get();

        $this->assertCount(2, $aprovados);
        foreach ($aprovados as $comp) {
            $this->assertEquals('aprovado', $comp->status);
        }
    }
    */
}