<?php

namespace Tests\Unit\Disciplines;

use Tests\TestCase;
use App\Http\Requests\StoreCurricularComponentRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola; 
use App\Models\Municipio;

class StoreCurricularComponentRequestTest extends TestCase
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
        $request = new StoreCurricularComponentRequest();
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
        $request = new StoreCurricularComponentRequest();
        $dados = $this->getValidData();
        unset($dados['id_escola']); 
        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails());
    }

    /**
     * @test
     */
    public function validacao_passa_com_dados_validos_com_escola()
    {
        $request = new StoreCurricularComponentRequest();
        $dados = $this->getValidData();
        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails());
    }

    /**
     * @test
     */
    public function validacao_falha_com_status_invalido()
    {
        $request = new StoreCurricularComponentRequest();
        $dados = $this->getValidData();
        $dados['status'] = 'em_revisao'; 
        $validator = Validator::make($dados, $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    /**
     * @test
     */
    public function validacao_falha_se_escola_opcional_nao_existe()
    {
        $request = new StoreCurricularComponentRequest();
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
            'nome' => 'Matemática Avançada',
            'carga_horaria' => '60h',
            'descricao' => 'Descrição da disciplina',
            'status' => 'aprovado', 
            'id_escola' => $escolaId,
        ];
    }

    public static function validationProvider(): array
    {
        return [
            'nome ausente' => ['nome', ''],
            'carga horaria ausente' => ['carga_horaria', ''],
        ];
    }
}
