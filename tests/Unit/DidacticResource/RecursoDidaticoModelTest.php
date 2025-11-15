<?php

namespace Tests\Unit\DidacticResource;

use Tests\TestCase;
use App\Models\RecursoDidatico;
use App\Models\Escola;
use App\Models\Usuario;
use App\Models\Agendamento;
use App\Models\Municipio;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\OfertaComponente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecursoDidaticoModelTest extends TestCase
{
    use RefreshDatabase;

    private Escola $escola;
    private Usuario $criador;
    private OfertaComponente $oferta;

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
        
        $this->criador = Usuario::factory()->create([
            'tipo_usuario' => 'administrador'
        ]);
        
        $professor = Usuario::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'tipo_usuario' => 'professor',
            'status_aprovacao' => 'ativo'
        ]);

        $componente = ComponenteCurricular::create([
            'nome' => 'Matemática',
            'carga_horaria' => 60,
            'id_escola' => $this->escola->id_escola,
            'status' => 'aprovado'
        ]);

        $turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);

        $this->oferta = OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_componente' => $componente->id_componente,
            'id_professor' => $professor->id_usuario
        ]);
    }

    public function teste_modelo_recurso_didatico_usa_tabela_correta()
    {
        $recurso = new RecursoDidatico();
        $this->assertEquals('recursos_didaticos', $recurso->getTable());
    }

    public function teste_modelo_recurso_didatico_usa_chave_primaria_correta()
    {
        $recurso = new RecursoDidatico();
        $this->assertEquals('id_recurso', $recurso->getKeyName());
    }

    public function teste_modelo_recurso_didatico_possui_propriedades_preenchiveis_corretas()
    {
        $recurso = new RecursoDidatico();
        $expected = [
            'nome',
            'tipo',
            'marca',
            'numero_serie',
            'quantidade',
            'observacoes',
            'data_aquisicao',
            'status',
            'id_escola',
            'id_usuario_criador',
        ];
        $this->assertEquals($expected, $recurso->getFillable());
    }

    public function teste_modelo_recurso_didatico_possui_timestamps()
    {
        $recurso = new RecursoDidatico();
        $this->assertTrue($recurso->usesTimestamps());
    }

    public function teste_modelo_recurso_didatico_possui_relacionamento_escola()
    {
        $recurso = RecursoDidatico::factory()->create(['id_escola' => $this->escola->id_escola]);
        
        $this->assertInstanceOf(BelongsTo::class, $recurso->escola());
        $this->assertTrue($recurso->escola->is($this->escola));
    }

    public function teste_modelo_recurso_didatico_pode_ser_global()
    {
        $recurso = RecursoDidatico::factory()->create(['id_escola' => null]);
        
        $this->assertNull($recurso->id_escola);
        $this->assertNull($recurso->escola);
    }

    public function teste_modelo_recurso_didatico_possui_relacionamento_criador()
    {
        $recurso = RecursoDidatico::factory()->create(['id_usuario_criador' => $this->criador->id_usuario]);

        $this->assertInstanceOf(BelongsTo::class, $recurso->criador());
        $this->assertTrue($recurso->criador->is($this->criador));
    }

    public function teste_modelo_recurso_didatico_possui_relacionamento_agendamentos()
    {
        $recurso = RecursoDidatico::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'status' => 'funcionando'
        ]);
        
        $agendamento = Agendamento::create([
            'id_recurso' => $recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => now()->addHour(),
            'data_hora_fim' => now()->addHours(2),
            'status' => 'agendado'
        ]);

        $this->assertTrue($recurso->agendamentos->contains($agendamento));
    }
}