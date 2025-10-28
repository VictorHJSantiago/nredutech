<?php

namespace Tests\Unit\SchoolClass;

use Tests\TestCase;
use App\Http\Requests\StoreSchoolClassRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola; 
use App\Models\Municipio;

class StoreSchoolClassRequestTest extends TestCase
{
    // use Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (!Escola::find(1)) {
            $municipio = Municipio::factory()->create();
            Escola::factory()->create(['id_escola' => 1, 'id_municipio' => $municipio->id_municipio]);
        }
    }

     /**
     * @test
     * @dataProvider 
     */
    public function campos_obrigatorios_falham_quando_ausentes($campo, $valorAusente)
    {
        $request = new StoreSchoolClassRequest();
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
        $request = new StoreSchoolClassRequest();
        $dados = $this->getValidData();
        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails());
    }

     /**
     * @test
     */
    public function validacao_falha_com_valores_invalidos()
    {
        $request = new StoreSchoolClassRequest();
        $dadosTurno = $this->getValidData();
        $dadosTurno['turno'] = 'integral';
        $validatorTurno = Validator::make($dadosTurno, $request->rules());
        $this->assertTrue($validatorTurno->fails());
        $this->assertArrayHasKey('turno', $validatorTurno->errors()->toArray());

        $dadosNivel = $this->getValidData();
        $dadosNivel['nivel_escolaridade'] = 'superior';
        $validatorNivel = Validator::make($dadosNivel, $request->rules());
        $this->assertTrue($validatorNivel->fails());
        $this->assertArrayHasKey('nivel_escolaridade', $validatorNivel->errors()->toArray());

        $dadosAno = $this->getValidData();
        $dadosAno['ano_letivo'] = 'abcd';
        $validatorAno = Validator::make($dadosAno, $request->rules());
        $this->assertTrue($validatorAno->fails());
        $this->assertArrayHasKey('ano_letivo', $validatorAno->errors()->toArray());
    }

    /**
     * @test
     */
    public function validacao_falha_se_escola_nao_existe()
    {
        $request = new StoreSchoolClassRequest();
        $dados = $this->getValidData();
        $dados['id_escola'] = 9999; 

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_escola', $validator->errors()->toArray());
    }

    private function getValidData(): array
    {
        $escolaId = Escola::first()->id_escola ?? 1;
        return [
            'serie' => '9º Ano A',
            'turno' => 'manha',
            'ano_letivo' => date('Y'),
            'nivel_escolaridade' => 'fundamental_2',
            'id_escola' => $escolaId,
        ];
    }

    public static function validationProvider(): array
    {
        return [
            'serie ausente' => ['serie', ''],
            'turno ausente' => ['turno', ''],
            'ano_letivo ausente' => ['ano_letivo', null],
            'nivel_escolaridade ausente' => ['nivel_escolaridade', ''],
            'id_escola ausente' => ['id_escola', null],
        ];
    }
}
