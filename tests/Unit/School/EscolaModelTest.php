<?php

namespace Tests\Unit\School;

use Tests\TestCase;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use PHPUnit\Framework\Attributes\Test;

class EscolaModelTest extends TestCase
{
    use RefreshDatabase;

    protected $municipio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);

        ComponenteCurricular::create([
            'nome' => 'Matemática',
            'status' => 'aprovado',
            'carga_horaria' => 60
        ]);
    }

    private function criarEscola(): Escola
    {
        return Escola::create([
            'nome' => 'Escola Modelo',
            'id_municipio' => $this->municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);
    }

    #[Test]
    public function escola_pertence_a_um_municipio()
    {
        $escola = $this->criarEscola();
        $this->assertInstanceOf(Municipio::class, $escola->municipio);
        $this->assertEquals($this->municipio->id_municipio, $escola->municipio->id_municipio);
    }

    #[Test]
    public function escola_pode_ter_muitos_usuarios()
    {
        $escola = $this->criarEscola();
        Usuario::factory(3)->create(['id_escola' => $escola->id_escola]);
        Usuario::factory()->create(); 

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $escola->usuarios);
        $this->assertCount(3, $escola->usuarios);
    }

     #[Test]
    public function escola_pode_ter_muitas_turmas()
    {
        $escola = $this->criarEscola();
        Usuario::factory()->create(['id_escola' => $escola->id_escola, 'tipo_usuario' => 'professor', 'status_aprovacao' => 'ativo']);

        Turma::factory(2)->create(['id_escola' => $escola->id_escola]);
        
        $outraEscola = $this->criarEscola();
        Usuario::factory()->create(['id_escola' => $outraEscola->id_escola, 'tipo_usuario' => 'professor', 'status_aprovacao' => 'ativo']);
        Turma::factory()->create(['id_escola' => $outraEscola->id_escola]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $escola->turmas);
        $this->assertCount(2, $escola->turmas);
    }

    #[Test]
    public function atributos_fillable_estao_corretos()
    {
        $escola = new Escola();
        $expected = [
            'nome',
            'endereco',
            'id_municipio',
            'id_diretor_responsavel',
            'nivel_ensino',
            'tipo',
        ];
        $this->assertEquals($expected, $escola->getFillable());
    }

    #[Test]
    public function chave_primaria_e_id_escola()
    {
        $escola = new Escola();
        $this->assertEquals('id_escola', $escola->getKeyName());
    }

     #[Test]
    public function usa_incrementing_ids_por_padrao()
     {
         $escola = new Escola();
         $this->assertTrue($escola->getIncrementing()); 
     }

     #[Test]
    public function timestamps_nao_sao_usados()
    {
        $escola = new Escola();
        $this->assertFalse($escola->usesTimestamps()); 
    }
}