<?php

namespace Tests\Unit\School; 

use Tests\TestCase;
use App\Http\Requests\UpdateSchoolRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Municipio;
use App\Models\Escola;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class UpdateSchoolRequestTest extends TestCase
{
    use RefreshDatabase; 

    protected $municipioValido;
    protected $escolaParaEditar;

    protected function setUp(): void
    {
        parent::setUp();
        $this->municipioValido = Municipio::create(['nome' => 'Municipio Valido', 'estado' => 'PR']);
        
        $this->escolaParaEditar = Escola::create([
            'nome' => 'Escola Original',
            'id_municipio' => $this->municipioValido->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);
    }

    #[Test]
    public function validacao_passa_atualizando_apenas_nome()
    {
        $request = new UpdateSchoolRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::get('escolas/{escola}', fn (Escola $escola) => $escola)
            ->bind('escola', $this->escolaParaEditar));

        $dados = ['nome' => 'Novo Nome Escola'];
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function validacao_passa_atualizando_apenas_tipo()
    {
        $request = new UpdateSchoolRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::get('escolas/{escola}', fn (Escola $escola) => $escola)
            ->bind('escola', $this->escolaParaEditar));

        $dados = ['tipo' => 'rural']; 
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    #[Test]
    #[DataProvider('invalidUpdateDataProvider')]
    public function validacao_falha_se_campo_enviado_for_invalido(array $dadosInvalidos)
    {
        $request = new UpdateSchoolRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::get('escolas/{escola}', fn (Escola $escola) => $escola)
            ->bind('escola', $this->escolaParaEditar));
        $validator = Validator::make($dadosInvalidos, $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey(key($dadosInvalidos), $validator->errors()->toArray());
    }

    #[Test]
    public function validacao_passa_com_todos_campos_validos()
    {
        $request = new UpdateSchoolRequest();
         $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::get('escolas/{escola}', fn (Escola $escola) => $escola)
            ->bind('escola', $this->escolaParaEditar));

        $novoMunicipio = Municipio::create(['nome' => 'Novo Municipio', 'estado' => 'SC']);
        $dados = [
            'nome' => 'Escola Completa Atualizada',
            'id_municipio' => $novoMunicipio->id_municipio,
            'nivel_ensino' => 'escola_municipal',
            'tipo' => 'rural'
        ];
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    public static function invalidUpdateDataProvider(): array
    {
        return [
            'nome vazio' => [['nome' => '']],
            'nivel invalido' => [['nivel_ensino' => 'superior_invalido']],
            'tipo invalido' => [['tipo' => 'metropolitana']],
            'municipio inexistente' => [['id_municipio' => 99999]],
            'nome muito longo' => [['nome' => str_repeat('Z', 256)]],
        ];
    }
}