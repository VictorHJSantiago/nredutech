<?php

namespace Tests\Unit\Appointments; 

use Tests\TestCase;
use App\Models\Agendamento;
use App\Models\RecursoDidatico;
use App\Models\OfertaComponente;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\Municipio;
use App\Models\ComponenteCurricular;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AgendamentoModelTest extends TestCase
{
    use RefreshDatabase;

    protected $recurso;
    protected $oferta;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $professor = Usuario::factory()->create(['id_escola' => $escola->id_escola]);
        $turma = Turma::factory()->create(['id_escola' => $escola->id_escola]);
        $componente = ComponenteCurricular::factory()->create();
        $this->recurso = RecursoDidatico::factory()->create();
        $this->oferta = OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_professor' => $professor->id_usuario,
            'id_componente' => $componente->id_componente
        ]);
    }

    /** @test */
    public function agendamento_pertence_a_um_recurso()
    {
        $agendamento = Agendamento::factory()->create([
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta
        ]);

        $this->assertInstanceOf(RecursoDidatico::class, $agendamento->recurso);
        $this->assertEquals($this->recurso->id_recurso, $agendamento->recurso->id_recurso);
    }

    /** @test */
    public function agendamento_pertence_a_uma_oferta()
    {
        $agendamento = Agendamento::factory()->create([
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta
        ]);

        $this->assertInstanceOf(OfertaComponente::class, $agendamento->oferta);
        $this->assertEquals($this->oferta->id_oferta, $agendamento->oferta->id_oferta);
    }

    /** @test */
    public function atributos_fillable_estao_corretos()
    {
        $agendamento = new Agendamento();
        $expected = ['data_hora_inicio', 'data_hora_fim', 'status', 'id_oferta', 'id_recurso'];
        $this->assertEqualsCanonicalizing($expected, $agendamento->getFillable());
    }

    /** @test */
    public function chave_primaria_e_id_agendamento()
    {
        $agendamento = new Agendamento();
        $this->assertEquals('id_agendamento', $agendamento->getKeyName());
    }

    /** @test */
    public function casts_de_data_estao_configurados()
    {
        $agendamento = new Agendamento();
        $casts = $agendamento->getCasts();
        $this->assertEquals('datetime', $casts['data_hora_inicio']);
        $this->assertEquals('datetime', $casts['data_hora_fim']);
    }

     /** @test */
    public function timestamps_sao_usados_por_padrao()
    {
        $agendamento = new Agendamento();
        $this->assertTrue($agendamento->usesTimestamps());
    }

    /*
    /** @test * /
    public function scope_futuros_retorna_apenas_agendamentos_futuros()
    {
        $agendamentoPassado = Agendamento::factory()->create([
            'data_hora_inicio' => Carbon::now()->subDay(),
            'data_hora_fim' => Carbon::now()->subDay()->addHour(),
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta
        ]);
         $agendamentoFuturo1 = Agendamento::factory()->create([
            'data_hora_inicio' => Carbon::now()->addDay(),
            'data_hora_fim' => Carbon::now()->addDay()->addHour(),
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta
        ]);
        $agendamentoFuturo2 = Agendamento::factory()->create([
            'data_hora_inicio' => Carbon::now()->addWeek(),
            'data_hora_fim' => Carbon::now()->addWeek()->addHour(),
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta
        ]);

        $futuros = Agendamento::futuros()->get(); // Supondo que o scope exista

        $this->assertCount(2, $futuros);
        $this->assertFalse($futuros->contains($agendamentoPassado));
        $this->assertTrue($futuros->contains($agendamentoFuturo1));
        $this->assertTrue($futuros->contains($agendamentoFuturo2));
    }
    */
}