<?php

namespace Tests\Unit\Appointments;

use Tests\TestCase;
use App\Http\Requests\StoreAppointmentRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\RecursoDidatico;
use App\Models\OfertaComponente;
use App\Models\Turma;
use App\Models\Escola;
use App\Models\Usuario;
use App\Models\ComponenteCurricular;
use App\Models\Municipio;
use Carbon\Carbon;

class StoreAppointmentRequestTest extends TestCase
{
    // use Illuminate\Foundation\Testing\RefreshDatabase;

    protected $recurso;
    protected $oferta;

    protected function setUp(): void
    {
        parent::setUp();
        if (!RecursoDidatico::find(1) || !OfertaComponente::find(1)) {
            $municipio = Municipio::factory()->create();
            $escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
            $professor = Usuario::factory()->create(['id_escola' => $escola->id_escola, 'tipo_usuario' => 'professor']);
            $turma = Turma::factory()->create(['id_escola' => $escola->id_escola]);
            $componente = ComponenteCurricular::factory()->create(['id_escola' => null]); 
            $this->recurso = RecursoDidatico::factory()->create(['id_recurso' => 1, 'id_escola' => null]);
            $this->oferta = OfertaComponente::factory()->create([
                'id_oferta' => 1,
                'id_turma' => $turma->id_turma,
                'id_professor' => $professor->id_usuario,
                'id_componente' => $componente->id_componente
            ]);
        } else {
             $this->recurso = RecursoDidatico::find(1);
             $this->oferta = OfertaComponente::find(1);
        }
    }

    /**
     * @test
     * @dataProvider 
     */
    public function campos_obrigatorios_falham_quando_ausentes($campo, $valorAusente)
    {
        $request = new StoreAppointmentRequest();
        $dados = $this->getValidData();
        $dados[$campo] = $valorAusente;
        $validator = Validator::make($dados, $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($campo, $validator->errors()->toArray());
    }

    /**
     * @test
     */
    public function validacao_passa_com_dados_validos()
    {
        $request = new StoreAppointmentRequest();
        $dados = $this->getValidData();
        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails());
    }

     /**
     * @test
     */
    public function validacao_falha_com_valores_invalidos()
    {
        $request = new StoreAppointmentRequest();
        $dadosInicioInv = $this->getValidData();
        $dadosInicioInv['data_hora_inicio'] = 'data invalida';
        $validatorInicioInv = Validator::make($dadosInicioInv, $request->rules());
        $this->assertTrue($validatorInicioInv->fails());
        $this->assertArrayHasKey('data_hora_inicio', $validatorInicioInv->errors()->toArray());
        $dadosFimInv = $this->getValidData();
        $dadosFimInv['data_hora_fim'] = 'data invalida';
        $validatorFimInv = Validator::make($dadosFimInv, $request->rules());
        $this->assertTrue($validatorFimInv->fails());
        $this->assertArrayHasKey('data_hora_fim', $validatorFimInv->errors()->toArray());
        $dadosRecursoInv = $this->getValidData();
        $dadosRecursoInv['id_recurso'] = 9999;
        $validatorRecursoInv = Validator::make($dadosRecursoInv, $request->rules());
        $this->assertTrue($validatorRecursoInv->fails());
        $this->assertArrayHasKey('id_recurso', $validatorRecursoInv->errors()->toArray());
        $dadosOfertaInv = $this->getValidData();
        $dadosOfertaInv['id_oferta'] = 9999;
        $validatorOfertaInv = Validator::make($dadosOfertaInv, $request->rules());
        $this->assertTrue($validatorOfertaInv->fails());
        $this->assertArrayHasKey('id_oferta', $validatorOfertaInv->errors()->toArray());
    }

     /**
     * @test
     */
    public function data_fim_deve_ser_depois_ou_igual_a_data_inicio()
    {
        $request = new StoreAppointmentRequest();
        $dados = $this->getValidData();
        $dados['data_hora_fim'] = Carbon::parse($dados['data_hora_inicio'])->subHour()->toDateTimeString(); 
        $validator = Validator::make($dados, $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('data_hora_fim', $validator->errors()->toArray());
    }

    private function getValidData(): array
    {
        $inicio = Carbon::now()->addDay()->hour(10)->minute(0)->second(0);
        return [
            'data_hora_inicio' => $inicio->toDateTimeString(),
            'data_hora_fim' => $inicio->copy()->addHour()->toDateTimeString(),
            'id_recurso' => $this->recurso->id_recurso ?? 1,
            'id_oferta' => $this->oferta->id_oferta ?? 1,
        ];
    }

    public static function validationProvider(): array
    {
        return [
            'data inicio ausente' => ['data_hora_inicio', ''],
            'data fim ausente' => ['data_hora_fim', ''],
            'id_recurso ausente' => ['id_recurso', null],
            'id_oferta ausente' => ['id_oferta', null],
        ];
    }
}
