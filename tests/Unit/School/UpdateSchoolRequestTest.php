<?php

namespace Tests\Unit\School; 

use Tests\TestCase;
use App\Http\Requests\UpdateSchoolRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Municipio;
use App\Models\Escola;
use Illuminate\Foundation\Testing\RefreshDatabase; 

class UpdateSchoolRequestTest extends TestCase
{
    use RefreshDatabase; 

    protected $municipioValido;
    protected $escolaParaEditar;

    protected function setUp(): void
    {
        parent::setUp();
        $this->municipioValido = Municipio::factory()->create();
        $this->escolaParaEditar = Escola::factory()->create(['id_municipio' => $this->municipioValido->id_municipio]);
    }

    /**
     * @test
     */
    public function validacao_passa_atualizando_apenas_nome()
    {
        $request = new UpdateSchoolRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::get('escolas/{escola}', fn (Escola $escola) => $escola)
            ->bind('escola', $this->escolaParaEditar));

        $dados = ['nome' => 'Novo Nome Escola'];
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function validacao_passa_atualizando_apenas_localizacao()
    {
        $request = new UpdateSchoolRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::get('escolas/{escola}', fn (Escola $escola) => $escola)
            ->bind('escola', $this->escolaParaEditar));

        $dados = ['localizacao' => 'rural']; 
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    /**
     * @test
     * @dataProvider
     */
    public function validacao_falha_se_campo_enviado_for_invalido(array $dadosInvalidos)
    {
        $request = new UpdateSchoolRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::get('escolas/{escola}', fn (Escola $escola) => $escola)
            ->bind('escola', $this->escolaParaEditar));
        $validator = Validator::make($dadosInvalidos, $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey(key($dadosInvalidos), $validator->errors()->toArray());
    }

    /**
     * @test
     */
    public function validacao_passa_com_todos_campos_validos()
    {
        $request = new UpdateSchoolRequest();
         $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::get('escolas/{escola}', fn (Escola $escola) => $escola)
            ->bind('escola', $this->escolaParaEditar));

        $novoMunicipio = Municipio::factory()->create();
        $dados = [
            'nome' => 'Escola Completa Atualizada',
            'id_municipio' => $novoMunicipio->id_municipio,
            'nivel_ensino' => 'fundamental_1',
            'localizacao' => 'rural'
        ];
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    public static function invalidUpdateDataProvider(): array
    {
        return [
            'nome vazio' => [['nome' => '']],
            'nivel invalido' => [['nivel_ensino' => 'superior_invalido']],
            'localizacao invalida' => [['localizacao' => 'metropolitana']],
            'municipio inexistente' => [['id_municipio' => 99999]],
            'nome muito longo' => [['nome' => str_repeat('Z', 256)]],
        ];
    }
}