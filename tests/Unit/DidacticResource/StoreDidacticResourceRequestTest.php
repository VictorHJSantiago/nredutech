<?php

namespace Tests\Unit\DidacticResource;

use Tests\TestCase;
use App\Http\Requests\StoreDidacticResourceRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola; 
use App\Models\Municipio;

class StoreDidacticResourceRequestTest extends TestCase
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
        $request = new StoreDidacticResourceRequest();
        $dados = $this->getValidData();
        $dados[$campo] = $valorAusente;

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($campo, $validator->errors()->toArray());
    }

     /**
     * @test
     */
    public function validacao_passa_com_dados_validos_sem_escola()
    {
        $request = new StoreDidacticResourceRequest();
        $dados = $this->getValidData();
        unset($dados['id_escola']); // 
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    /**
     * @test
     */
    public function validacao_passa_com_dados_validos_com_escola()
    {
        $request = new StoreDidacticResourceRequest();
        $dados = $this->getValidData();
        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails());
    }

    /**
     * @test
     */
    public function validacao_falha_com_valores_invalidos()
    {
        $request = new StoreDidacticResourceRequest();
        $dadosTipo = $this->getValidData();
        $dadosTipo['tipo'] = 'escritorio';
        $validatorTipo = Validator::make($dadosTipo, $request->rules());
        $this->assertTrue($validatorTipo->fails());
        $this->assertArrayHasKey('tipo', $validatorTipo->errors()->toArray());
        $dadosQtd = $this->getValidData();
        $dadosQtd['quantidade'] = 'abc';
        $validatorQtd = Validator::make($dadosQtd, $request->rules());
        $this->assertTrue($validatorQtd->fails());
        $this->assertArrayHasKey('quantidade', $validatorQtd->errors()->toArray());

        $dadosQtdNeg = $this->getValidData();
        $dadosQtdNeg['quantidade'] = -1;
        $validatorQtdNeg = Validator::make($dadosQtdNeg, $request->rules());
        $this->assertTrue($validatorQtdNeg->fails());
        $this->assertArrayHasKey('quantidade', $validatorQtdNeg->errors()->toArray());

        $dadosData = $this->getValidData();
        $dadosData['data_aquisicao'] = '31/02/2024'; 
        $validatorData = Validator::make($dadosData, $request->rules());
        $this->assertTrue($validatorData->fails());
        $this->assertArrayHasKey('data_aquisicao', $validatorData->errors()->toArray());

        $dadosStatus = $this->getValidData();
        $dadosStatus['status'] = 'excelente';
        $validatorStatus = Validator::make($dadosStatus, $request->rules());
        $this->assertTrue($validatorStatus->fails());
        $this->assertArrayHasKey('status', $validatorStatus->errors()->toArray());
    }

    /**
     * @test
     */
    public function validacao_falha_se_escola_opcional_nao_existe()
    {
        $request = new StoreDidacticResourceRequest();
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
            'nome' => 'Projetor XYZ',
            'tipo' => 'didatico',
            'marca' => 'Marca Exemplo',
            'numero_serie' => 'SN123456',
            'quantidade' => 5,
            'observacoes' => 'Observação teste',
            'data_aquisicao' => '2024-01-15',
            'status' => 'funcionando',
            'id_escola' => $escolaId,
            'split_quantity' => 'false', 
        ];
    }

    public static function validationProvider(): array
    {
        return [
            'nome ausente' => ['nome', ''],
            'tipo ausente' => ['tipo', ''],
            'quantidade ausente' => ['quantidade', null],
            'status ausente' => ['status', ''],
        ];
    }
}
