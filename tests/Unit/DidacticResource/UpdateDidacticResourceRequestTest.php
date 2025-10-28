<?php

namespace Tests\Unit\DidacticResource; 

use Tests\TestCase;
use App\Http\Requests\UpdateDidacticResourceRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\RecursoDidatico;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateDidacticResourceRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $escola;
    protected $recursoParaEditar;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $this->escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $criador = Usuario::factory()->create();
        $this->recursoParaEditar = RecursoDidatico::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $criador->id_usuario
        ]);
    }

    /**
     * @test
     */
    public function validacao_passa_atualizando_apenas_nome()
    {
        $request = new UpdateDidacticResourceRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('recursos-didaticos/{recursoDidatico}', fn (RecursoDidatico $recursoDidatico) => $recursoDidatico)
            ->bind('recursoDidatico', $this->recursoParaEditar));
        $dados = ['nome' => 'Nome Recurso Atualizado']; 
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

     /**
     * @test
     */
    public function validacao_passa_atualizando_apenas_status()
    {
        $request = new UpdateDidacticResourceRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('recursos-didaticos/{recursoDidatico}', fn (RecursoDidatico $recursoDidatico) => $recursoDidatico)
            ->bind('recursoDidatico', $this->recursoParaEditar));

        $dados = ['status' => 'quebrado']; 
        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails());
    }


    /**
     * @test
     * @dataProvider invalidUpdateDataProvider
     */
    public function validacao_falha_se_campo_enviado_for_invalido(array $dadosInvalidos)
    {
        $request = new UpdateDidacticResourceRequest();
         $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('recursos-didaticos/{recursoDidatico}', fn (RecursoDidatico $recursoDidatico) => $recursoDidatico)
            ->bind('recursoDidatico', $this->recursoParaEditar));

        $validator = Validator::make($dadosInvalidos, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey(key($dadosInvalidos), $validator->errors()->toArray());
    }

    /**
     * @test
     */
    public function validacao_passa_com_todos_campos_validos()
    {
        $request = new UpdateDidacticResourceRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('recursos-didaticos/{recursoDidatico}', fn (RecursoDidatico $recursoDidatico) => $recursoDidatico)
            ->bind('recursoDidatico', $this->recursoParaEditar));

        $outraEscola = Escola::factory()->create(['id_municipio' => $this->escola->id_municipio]);
        $dados = [
            'nome' => 'Recurso Completo Editado',
            'tipo' => 'laboratorio',
            'marca' => 'Nova Marca',
            'numero_serie' => 'SN9876',
            'quantidade' => 10,
            'observacoes' => 'Atualizado completo',
            'data_aquisicao' => '2022-11-01',
            'status' => 'descartado',
            'id_escola' => $outraEscola->id_escola,
        ];
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    public static function invalidUpdateDataProvider(): array
    {
        return [
            'nome vazio' => [['nome' => '']], 
            'tipo invalido' => [['tipo' => 'veiculo']],
            'quantidade invalida' => [['quantidade' => 'dez']],
            'quantidade negativa' => [['quantidade' => -5]],
            'data invalida' => [['data_aquisicao' => '15/30/2024']],
            'status invalido' => [['status' => 'novo']],
            'escola inexistente' => [['id_escola' => 99999]],
            'nome muito longo' => [['nome' => str_repeat('R', 256)]],
            'marca muito longa' => [['marca' => str_repeat('M', 256)]],
            'num serie muito longo' => [['numero_serie' => str_repeat('S', 256)]],
        ];
    }
}