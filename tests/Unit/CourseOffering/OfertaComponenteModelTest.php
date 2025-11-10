<?php

namespace Tests\Unit\CourseOffering;

use Tests\TestCase;
use App\Models\OfertaComponente;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\Usuario;
use App\Models\Agendamento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\RecursoDidatico;

class OfertaComponenteModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $escola = Escola::create([
            'nome' => 'Escola Teste',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);

        $criador = Usuario::factory()->create();

        Usuario::factory()->create([
            'id_escola' => $escola->id_escola,
            'tipo_usuario' => 'professor',
            'status_aprovacao' => 'ativo'
        ]);
        ComponenteCurricular::create([
            'nome' => 'Matemática',
            'status' => 'aprovado',
            'carga_horaria' => 60,
            'id_escola' => $escola->id_escola,
            'id_usuario_criador' => $criador->id_usuario,
        ]);

        RecursoDidatico::create([
            'nome' => 'Projetor',
            'quantidade' => 1,
            'tipo' => 'equipamento',
            'status' => 'funcionando',
            'id_escola' => $escola->id_escola,
            'id_usuario_criador' => $criador->id_usuario,
        ]);

        Turma::factory()->create(['id_escola' => $escola->id_escola]);
    }

    public function test_oferta_componente_model_uses_correct_table()
    {
        $oferta = new OfertaComponente();
        $this->assertEquals('oferta_componentes', $oferta->getTable());
    }

    public function test_oferta_componente_model_uses_correct_primary_key()
    {
        $oferta = new OfertaComponente();
        $this->assertEquals('id_oferta', $oferta->getKeyName());
    }

    public function test_oferta_componente_model_has_correct_fillable_properties()
    {
        $oferta = new OfertaComponente();
        $expected = [
            'id_turma',
            'id_componente',
            'id_professor',
        ];
        $this->assertEquals($expected, $oferta->getFillable());
    }

    public function test_oferta_componente_model_timestamps_are_disabled()
    {
        $oferta = new OfertaComponente();
        $this->assertTrue($oferta->usesTimestamps());
    }

    public function test_oferta_componente_model_has_turma_relationship()
    {
        $turma = Turma::factory()->create();
        $oferta = OfertaComponente::factory()->create(['id_turma' => $turma->id_turma]);

        $this->assertInstanceOf(BelongsTo::class, $oferta->turma());
        $this->assertTrue($oferta->turma->is($turma));
    }

    public function test_oferta_componente_model_has_componente_curricular_relationship()
    {
        $componente = ComponenteCurricular::factory()->create();
        $oferta = OfertaComponente::factory()->create(['id_componente' => $componente->id_componente]);

        $this->assertInstanceOf(BelongsTo::class, $oferta->componenteCurricular());
        $this->assertTrue($oferta->componenteCurricular->is($componente));
    }

    public function test_oferta_componente_model_has_professor_relationship()
    {
        $professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'status_aprovacao' => 'ativo']);
        $oferta = OfertaComponente::factory()->create(['id_professor' => $professor->id_usuario]);

        $this->assertInstanceOf(BelongsTo::class, $oferta->professor());
        $this->assertTrue($oferta->professor->is($professor));
    }

    public function test_oferta_componente_model_has_agendamentos_relationship()
    {
        $oferta = OfertaComponente::factory()->create();
        Agendamento::factory(3)->create(['id_oferta' => $oferta->id_oferta]);

        $this->assertInstanceOf(HasMany::class, $oferta->agendamentos());
        $this->assertEquals(3, $oferta->agendamentos->count());
        $this->assertInstanceOf(Agendamento::class, $oferta->agendamentos->first());
    }
}