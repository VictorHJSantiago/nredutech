<?php

namespace Tests\Unit\Appointments; 

use Tests\TestCase;
use App\Http\Requests\UpdateAppointmentRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\RecursoDidatico;
use App\Models\OfertaComponente;
use App\Models\Agendamento; 
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\Municipio;
use App\Models\ComponenteCurricular;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;


class UpdateAppointmentRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $recurso;
    protected $oferta;
    protected $agendamentoParaEditar;

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
        $this->agendamentoParaEditar = Agendamento::factory()->create([
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta
        ]);
    }

    /**
     * @test
     */
    public function validacao_passa_atualizando_apenas_data_inicio()
    {
        $request = new UpdateAppointmentRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('agendamentos/{agendamento}', fn (Agendamento $agendamento) => $agendamento)
            ->bind('agendamento', $this->agendamentoParaEditar));
        $novaDataInicio = Carbon::parse($this->agendamentoParaEditar->data_hora_fim)->addHour()->toDateTimeString();
        $dados = ['data_hora_inicio' => $novaDataInicio]; 
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    /**
     * @test
     */
    public function validacao_passa_atualizando_apenas_recurso()
    {
        $request = new UpdateAppointmentRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('agendamentos/{agendamento}', fn (Agendamento $agendamento) => $agendamento)
            ->bind('agendamento', $this->agendamentoParaEditar));

        $novoRecurso = RecursoDidatico::factory()->create();
        $dados = ['id_recurso' => $novoRecurso->id_recurso]; 

        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails());
    }


    /**
     * @test
     * @dataProvider 
     */
    public function validacao_falha_se_campo_enviado_for_invalido(array $dadosInvalidos)
    {
        $request = new UpdateAppointmentRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('agendamentos/{agendamento}', fn (Agendamento $agendamento) => $agendamento)
            ->bind('agendamento', $this->agendamentoParaEditar));

        $validator = Validator::make($dadosInvalidos, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey(key($dadosInvalidos), $validator->errors()->toArray());
    }

    /**
     * @test
     */
    public function data_fim_deve_ser_maior_ou_igual_inicio_quando_ambas_enviadas()
    {
        $request = new UpdateAppointmentRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('agendamentos/{agendamento}', fn (Agendamento $agendamento) => $agendamento)
            ->bind('agendamento', $this->agendamentoParaEditar));

        $inicio = Carbon::now()->addDay()->hour(11);
        $dados = [
            'data_hora_inicio' => $inicio->toDateTimeString(),
            'data_hora_fim' => $inicio->copy()->subMinute()->toDateTimeString() 
        ];

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('data_hora_fim', $validator->errors()->toArray());
    }

    public static function invalidUpdateDataProvider(): array
    {
        return [
            'data inicio invalida' => [['data_hora_inicio' => 'ontem']],
            'data fim invalida' => [['data_hora_fim' => 'amanha a tarde']],
            'recurso inexistente' => [['id_recurso' => 99999]],
            'oferta inexistente' => [['id_oferta' => 99999]],
        ];
    }
}