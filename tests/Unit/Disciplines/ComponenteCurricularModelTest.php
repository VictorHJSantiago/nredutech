<?php

namespace Tests\Unit\Disciplines;

use Tests\TestCase;
use App\Models\ComponenteCurricular;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Turma;
use App\Models\Usuario;
use App\Models\OfertaComponente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ComponenteCurricularModelTest extends TestCase
{
    use RefreshDatabase;

    protected $escola;
    protected $turma;
    protected $professor;

    protected function setUp(): void
    {
        parent::setUp();
        
        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        
        $this->escola = Escola::create([
            'nome' => 'Escola Teste',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);

        $this->professor = Usuario::factory()->create([
             'id_escola' => $this->escola->id_escola,
             'tipo_usuario' => 'professor',
             'status_aprovacao' => 'ativo'
        ]);
        
        ComponenteCurricular::create([
            'nome' => 'Matemática Base',
            'descricao' => 'Descrição Padrão',
            'carga_horaria' => 60,
            'status' => 'aprovado',
        ]);

        $this->turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
    }

    private function criarComponente(array $options = []): ComponenteCurricular
    {
        return ComponenteCurricular::create(array_merge([
            'nome' => 'Matemática',
            'descricao' => 'Descrição Padrão',
            'carga_horaria' => 60,
            'status' => 'aprovado',
            'id_escola' => $this->escola->id_escola,
        ], $options));
    }

    #[Test]
    public function testa_modelo_componente_curricular_usa_tabela_correta()
    {
        $componente = new ComponenteCurricular();
        $this->assertEquals('componentes_curriculares', $componente->getTable());
    }

    #[Test]
    public function testa_modelo_componente_curricular_usa_chave_primaria_correta()
    {
        $componente = new ComponenteCurricular();
        $this->assertEquals('id_componente', $componente->getKeyName());
    }

    #[Test]
    public function testa_modelo_componente_curricular_tem_propriedades_fillable_corretas()
    {
        $componente = new ComponenteCurricular();
        $expected = [
            'nome',
            'descricao',
            'carga_horaria',
            'status',
            'id_usuario_criador',
            'id_escola',
        ];
        $this->assertEquals($expected, $componente->getFillable());
    }

    #[Test]
    public function testa_modelo_componente_curricular_timestamps_sao_usados()
    {
        $componente = new ComponenteCurricular();
        $this->assertTrue($componente->usesTimestamps());
    }

    #[Test]
    public function testa_modelo_componente_curricular_tem_relacionamento_escola()
    {
        $componente = $this->criarComponente();
        $this->assertInstanceOf(Escola::class, $componente->escola);
        $this->assertEquals($this->escola->id_escola, $componente->escola->id_escola);
    }

    #[Test]
    public function testa_modelo_componente_curricular_tem_relacionamento_ofertas_componentes()
    {
        $componente = $this->criarComponente();
        
        OfertaComponente::factory(3)->create([
            'id_componente' => $componente->id_componente,
            'id_turma' => $this->turma->id_turma,
            'id_professor' => $this->professor->id_usuario
        ]);

        $componente->load('ofertas');

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $componente->ofertas);
        $this->assertCount(3, $componente->ofertas);
    }

    #[Test]
    public function testa_componente_curricular_pode_ser_global()
    {
        $componente = $this->criarComponente(['id_escola' => null]);
        
        $this->assertDatabaseHas('componentes_curriculares', [
            'id_componente' => $componente->id_componente,
            'id_escola' => null,
        ]);
        $this->assertNull($componente->escola);
    }
}