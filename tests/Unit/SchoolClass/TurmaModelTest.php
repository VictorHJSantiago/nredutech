<?php

namespace Tests\Unit\SchoolClass; 

use Tests\TestCase;
use App\Models\Turma;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\OfertaComponente;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TurmaModelTest extends TestCase
{
    use RefreshDatabase;

    protected $escola;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $this->escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
    }

    /** @test */
    public function turma_pertence_a_uma_escola()
    {
        $turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);

        $this->assertInstanceOf(Escola::class, $turma->escola);
        $this->assertEquals($this->escola->id_escola, $turma->escola->id_escola);
    }

    /** @test */
    public function turma_pode_ter_muitas_ofertas_componentes()
    {
        $turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        OfertaComponente::factory(3)->create(['id_turma' => $turma->id_turma]);
        $outraTurma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        OfertaComponente::factory()->create(['id_turma' => $outraTurma->id_turma]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $turma->ofertasComponentes);
        $this->assertCount(3, $turma->ofertasComponentes);
    }

    /** @test */
    public function atributos_fillable_estao_corretos()
    {
        $turma = new Turma();
        $expected = ['serie', 'turno', 'ano_letivo', 'nivel_escolaridade', 'id_escola'];
        $this->assertEquals($expected, $turma->getFillable());
    }

    /** @test */
    public function chave_primaria_e_id_turma()
    {
        $turma = new Turma();
        $this->assertEquals('id_turma', $turma->getKeyName());
    }

    /** @test */
    public function timestamps_sao_usados_por_padrao()
    {
        $turma = new Turma();
        $this->assertTrue($turma->usesTimestamps()); 
    }

    /*
    /** @test * /
    public function scope_doAnoLetivo_retorna_turmas_do_ano_especifico()
    {
        $anoAtual = date('Y');
        $anoAnterior = $anoAtual - 1;
        Turma::factory()->create(['id_escola' => $this->escola->id_escola, 'ano_letivo' => $anoAtual]);
        Turma::factory()->create(['id_escola' => $this->escola->id_escola, 'ano_letivo' => $anoAnterior]);
        Turma::factory()->create(['id_escola' => $this->escola->id_escola, 'ano_letivo' => $anoAtual]);

        $turmasAnoAtual = Turma::doAnoLetivo($anoAtual)->get();
        $turmasAnoAnterior = Turma::doAnoLetivo($anoAnterior)->get();

        $this->assertCount(2, $turmasAnoAtual);
        $this->assertCount(1, $turmasAnoAnterior);
    }
    */
}