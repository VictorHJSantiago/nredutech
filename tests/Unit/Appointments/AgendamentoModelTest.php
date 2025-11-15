<?php

namespace Tests\Unit\Appointments;

use Tests\TestCase;
use App\Models\Agendamento;
use App\Models\RecursoDidatico;
use App\Models\OfertaComponente;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendamentoModelTest extends TestCase
{
    use RefreshDatabase;

    protected $oferta;
    protected $recurso;

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

        $professor = Usuario::factory()->create([
            'id_escola' => $escola->id_escola,
            'tipo_usuario' => 'professor',
            'status_aprovacao' => 'ativo'
        ]);

        $componente = ComponenteCurricular::create([
            'nome' => 'Matemática',
            'carga_horaria' => 60,
            'id_escola' => $escola->id_escola,
            'status' => 'aprovado'
        ]);

        $turma = Turma::factory()->create(['id_escola' => $escola->id_escola]);

        $this->recurso = RecursoDidatico::factory()->create([
            'id_escola' => $escola->id_escola,
            'status' => 'funcionando'
        ]);
        
        $this->oferta = OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_componente' => $componente->id_componente,
            'id_professor' => $professor->id_usuario
        ]);
    }

    public function test_modelo_agendamento_usa_tabela_correta()
    {
        $agendamento = new Agendamento();
        $this->assertEquals('agendamentos', $agendamento->getTable());
    }

    public function test_modelo_agendamento_usa_chave_primaria_correta()
    {
        $agendamento = new Agendamento();
        $this->assertEquals('id_agendamento', $agendamento->getKeyName());
    }

    public function test_modelo_agendamento_tem_propriedades_fillable_corretas()
    {
        $agendamento = new Agendamento();
        $expected = [
            'data_hora_inicio',
            'data_hora_fim',
            'status',
            'id_recurso',
            'id_oferta',
        ];
        $this->assertEquals($expected, $agendamento->getFillable());
    }

    public function test_modelo_agendamento_tem_timestamps()
    {
        $agendamento = new Agendamento();
        $this->assertTrue($agendamento->usesTimestamps());
    }

    public function test_modelo_agendamento_converte_atributos_corretamente()
    {
        $agendamento = new Agendamento();
        $casts = $agendamento->getCasts();
        
        $this->assertArrayNotHasKey('data_hora_inicio', $casts);
        $this->assertArrayNotHasKey('data_hora_fim', $casts);
    }

    public function test_modelo_agendamento_tem_relacionamento_recurso()
    {
        $agendamento = Agendamento::create([
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => now()->addHour(),
            'data_hora_fim' => now()->addHours(2),
            'status' => 'agendado'
        ]);

        $this->assertInstanceOf(BelongsTo::class, $agendamento->recurso());
        $this->assertTrue($agendamento->recurso->is($this->recurso));
    }

    public function test_modelo_agendamento_tem_relacionamento_oferta()
    {
        $agendamento = Agendamento::create([
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => now()->addHour(),
            'data_hora_fim' => now()->addHours(2),
            'status' => 'agendado'
        ]);

        $this->assertInstanceOf(BelongsTo::class, $agendamento->oferta());
        $this->assertTrue($agendamento->oferta->is($this->oferta));
    }
}