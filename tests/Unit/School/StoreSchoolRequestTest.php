<?php

namespace Tests\Unit\School; 

use Tests\TestCase;
use App\Http\Requests\StoreSchoolRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Municipio;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class StoreSchoolRequestTest extends TestCase
{
    use RefreshDatabase; 

    protected $municipioValido;

    protected function setUp(): void
    {
        parent::setUp();
        $this->municipioValido = Municipio::create(['nome' => 'Municipio Valido', 'estado' => 'PR']);
    }

    #[Test]
    #[DataProvider('validationProviderObrigatorios')]
    public function campos_obrigatorios_falham_quando_ausentes($campo, $valorAusente)
    {
        $request = new StoreSchoolRequest();
        $dados = $this->getValidData();
        $dados[$campo] = $valorAusente;

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($campo, $validator->errors()->toArray());
    }

    #[Test]
    public function validacao_passa_com_dados_validos()
    {
        $request = new StoreSchoolRequest();
        $dados = $this->getValidData();
        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    #[Test]
    #[DataProvider('enumInvalidosProvider')]
    public function validacao_falha_com_valores_invalidos_para_enums($campo, $valorInvalido)
    {
        $request = new StoreSchoolRequest();
        $dados = $this->getValidData();
        $dados[$campo] = $valorInvalido;

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($campo, $validator->errors()->toArray());
    }

    #[Test]
    public function validacao_falha_se_municipio_nao_existe()
    {
        $request = new StoreSchoolRequest();
        $dados = $this->getValidData();
        $dados['id_municipio'] = 9999; 

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_municipio', $validator->errors()->toArray());
    }

    #[Test]
    public function nome_nao_pode_exceder_limite_maximo()
    {
        $request = new StoreSchoolRequest();
        $dados = $this->getValidData();
        $dados['nome'] = str_repeat('X', 256); 

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
    }

    private function getValidData(): array
    {
        return [
            'nome' => 'Escola Exemplo Válida',
            'id_municipio' => $this->municipioValido->id_municipio,
            'nivel_ensino' => 'colegio_estadual', 
            'tipo' => 'urbana', 
        ];
    }

    public static function validationProviderObrigatorios(): array
    {
        return [
            'nome ausente' => ['nome', ''],
            'municipio ausente' => ['id_municipio', null],
            'nivel ensino ausente' => ['nivel_ensino', ''],
            'tipo ausente' => ['tipo', ''],
        ];
    }

    public static function enumInvalidosProvider(): array
    {
        return [
            'nivel ensino invalido' => ['nivel_ensino', 'superior'],
            'tipo invalido' => ['tipo', 'metropolitana'],
        ];
    }
}