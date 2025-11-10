<?php

namespace Tests\Unit\SchoolClass; 

use Tests\TestCase;
use App\Models\Turma;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\OfertaComponente;
use App\Models\Usuario;
use App\Models\ComponenteCurricular;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class TurmaModelTest extends TestCase
{
    use RefreshDatabase;

    protected $escola;

    protected function setUp(): void
    {
        parent::setUp();
        
        $municipio = Municipio::create(['nome' => 'Municipio Teste']);
        
        $this->escola = Escola::create([
            'nome' => 'Escola Teste',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);

        Usuario::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'tipo_usuario' => 'professor',
            'status_aprovacao' => 'ativo'
        ]);

        ComponenteCurricular::create([
            'nome' => 'Matemática',
            'status' => 'aprovado',
            'carga_horaria' => 60 
        ]);
    }

    #[Test]
    public function turma_pertence_a_uma_escola()
    {
        $turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);

        $this->assertInstanceOf(Escola::class, $turma->escola);
        $this->assertEquals($this->escola->id_escola, $turma->escola->id_escola);
    }

    #[Test]
    public function turma_pode_ter_muitas_ofertas_componentes()
    {
        $turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        OfertaComponente::factory(3)->create(['id_turma' => $turma->id_turma]);
        $outraTurma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        OfertaComponente::factory()->create(['id_turma' => $outraTurma->id_turma]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $turma->ofertasComponentes);
        $this->assertCount(3, $turma->ofertasComponentes);
    }

    #[Test]
    public function atributos_fillable_estao_corretos()
    {
        $turma = new Turma();
        $expected = ['serie', 'turno', 'ano_letivo', 'nivel_escolaridade', 'id_escola'];
        $this->assertEquals($expected, $turma->getFillable());
    }

    #[Test]
    public function chave_primaria_e_id_turma()
    {
        $turma = new Turma();
        $this->assertEquals('id_turma', $turma->getKeyName());
    }

    #[Test]
    public function timestamps_sao_usados_por_padrao()
    {
        $turma = new Turma();
        $this->assertTrue($turma->usesTimestamps()); 
    }
}