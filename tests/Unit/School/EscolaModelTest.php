<?php

namespace Tests\Unit\School;

use Tests\TestCase;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use App\Models\Turma;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;

class EscolaModelTest extends TestCase
{
    use RefreshDatabase;

    protected $municipio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->municipio = Municipio::factory()->create();
    }

    /** @test */
    public function escola_pertence_a_um_municipio()
    {
        $escola = Escola::factory()->create(['id_municipio' => $this->municipio->id_municipio]);
        $this->assertInstanceOf(Municipio::class, $escola->municipio);
        $this->assertEquals($this->municipio->id_municipio, $escola->municipio->id_municipio);
    }

    /** @test */
    public function escola_pode_ter_muitos_usuarios()
    {
        $escola = Escola::factory()->create(['id_municipio' => $this->municipio->id_municipio]);
        Usuario::factory(3)->create(['id_escola' => $escola->id_escola]);
        Usuario::factory()->create(); // Admin sem escola

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $escola->usuarios);
        $this->assertCount(3, $escola->usuarios);
    }

     /** @test */
    public function escola_pode_ter_muitas_turmas()
    {
        $escola = Escola::factory()->create(['id_municipio' => $this->municipio->id_municipio]);
        Turma::factory(2)->create(['id_escola' => $escola->id_escola]);
        $outraEscola = Escola::factory()->create(['id_municipio' => $this->municipio->id_municipio]);
        Turma::factory()->create(['id_escola' => $outraEscola->id_escola]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $escola->turmas);
        $this->assertCount(2, $escola->turmas);
    }

    /** @test */
    public function atributos_fillable_estao_corretos()
    {
        $escola = new Escola();
        $expected = ['nome', 'id_municipio', 'nivel_ensino', 'localizacao'];
        $this->assertEquals($expected, $escola->getFillable());
    }

    /** @test */
    public function chave_primaria_e_id_escola()
    {
        $escola = new Escola();
        $this->assertEquals('id_escola', $escola->getKeyName());
    }

     /** @test */
    public function nao_usa_incrementing_ids_por_padrao_se_necessario()
     {
         $escola = new Escola();
         $this->assertTrue($escola->getIncrementing()); 
     }

     /** @test */
    public function timestamps_sao_usados_por_padrao()
    {
        $escola = new Escola();
        $this->assertTrue($escola->usesTimestamps()); 
    }

    /*
    /** @test * /
    public function scope_doNivel_retorna_escolas_do_nivel_especifico()
    {
        Escola::factory()->create(['id_municipio' => $this->municipio->id_municipio, 'nivel_ensino' => 'medio']);
        Escola::factory()->create(['id_municipio' => $this->municipio->id_municipio, 'nivel_ensino' => 'fundamental_2']);
        Escola::factory()->create(['id_municipio' => $this->municipio->id_municipio, 'nivel_ensino' => 'medio']);

        $escolasMedio = Escola::doNivel('medio')->get();
        $escolasFundamental = Escola::doNivel('fundamental_2')->get();

        $this->assertCount(2, $escolasMedio);
        $this->assertCount(1, $escolasFundamental);
    }
    */

}