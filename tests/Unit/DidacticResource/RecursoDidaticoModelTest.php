<?php

namespace Tests\Unit\DidacticResource; 

use Tests\TestCase;
use App\Models\RecursoDidatico;
use App\Models\Escola;
use App\Models\Usuario;
use App\Models\Agendamento;
use App\Models\Municipio; 
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecursoDidaticoModelTest extends TestCase
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
    public function recurso_pode_pertencer_a_uma_escola()
    {
        $recurso = RecursoDidatico::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $this->criador->id_usuario
        ]);

        $this->assertInstanceOf(Escola::class, $recurso->escola);
        $this->assertEquals($this->escola->id_escola, $recurso->escola->id_escola);
    }

     /** @test */
    public function recurso_pode_ser_global_sem_escola()
    {
        $recurso = RecursoDidatico::factory()->create([
            'id_escola' => null, 
            'id_usuario_criador' => $this->criador->id_usuario 
        ]);

        $this->assertNull($recurso->escola);
    }

    /** @test */
    public function recurso_pertence_a_um_criador()
    {
        $recurso = RecursoDidatico::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $this->criador->id_usuario
        ]);

        $this->assertInstanceOf(Usuario::class, $recurso->criador);
        $this->assertEquals($this->criador->id_usuario, $recurso->criador->id_usuario);
    }

     /** @test */
    public function recurso_pode_ter_muitos_agendamentos()
    {
        $recurso = RecursoDidatico::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $this->criador->id_usuario
        ]);
        Agendamento::factory(3)->create(['id_recurso' => $recurso->id_recurso]);
        $outroRecurso = RecursoDidatico::factory()->create();
        Agendamento::factory()->create(['id_recurso' => $outroRecurso->id_recurso]);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $recurso->agendamentos);
        $this->assertCount(3, $recurso->agendamentos);
    }


    /** @test */
    public function atributos_fillable_estao_corretos()
    {
        $recurso = new RecursoDidatico();
        $expected = [
            'nome', 'tipo', 'marca', 'numero_serie', 'quantidade',
            'observacoes', 'data_aquisicao', 'status', 'id_usuario_criador', 'id_escola'
        ];
        $this->assertEqualsCanonicalizing($expected, $recurso->getFillable());
    }

    /** @test */
    public function chave_primaria_e_id_recurso()
    {
        $recurso = new RecursoDidatico();
        $this->assertEquals('id_recurso', $recurso->getKeyName());
    }

    /** @test */
    public function timestamps_sao_usados_por_padrao()
    {
        $recurso = new RecursoDidatico();
        $this->assertTrue($recurso->usesTimestamps());
    }

    /*
    /** @test * /
    public function scope_funcionando_retorna_apenas_recursos_funcionando()
    {
        RecursoDidatico::factory()->create(['status' => 'funcionando']);
        RecursoDidatico::factory()->create(['status' => 'em_manutencao']);
        RecursoDidatico::factory()->create(['status' => 'funcionando']);
        RecursoDidatico::factory()->create(['status' => 'quebrado']);

        $funcionando = RecursoDidatico::funcionando()->get();

        $this->assertCount(2, $funcionando);
        foreach ($funcionando as $rec) {
            $this->assertEquals('funcionando', $rec->status);
        }
    }
    */
}