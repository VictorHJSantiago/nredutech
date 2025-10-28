<?php

namespace Tests\Unit\Disciplines;

use Tests\TestCase;
use App\Http\Requests\UpdateCurricularComponentRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola; 
use App\Models\Municipio;
use App\Models\ComponenteCurricular;

class UpdateCurricularComponentRequestTest extends TestCase
{
    // use Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (!Escola::find(1)) {
             $municipio = Municipio::factory()->create();
             Escola::factory()->create(['id_escola' => 1, 'id_municipio' => $municipio->id_municipio]);
        }
         if (!ComponenteCurricular::find(1)) {
            ComponenteCurricular::factory()->create(['id_componente' => 1]);
        }
    }

     /**
     * @test
     */
    public function validacao_passa_atualizando_apenas_status()
    {
        $request = new UpdateCurricularComponentRequest();
        $componenteParaEditar = ComponenteCurricular::find(1);
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('componentes/{componente}', fn (ComponenteCurricular $componente) => $componente)
            ->bind('componente', $componenteParaEditar));
        $dados = ['status' => 'reprovado']; 
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

     /**
     * @test
     */
    public function validacao_falha_se_campo_enviado_for_invalido()
    {
        $request = new UpdateCurricularComponentRequest();
        $componenteParaEditar = ComponenteCurricular::find(1);
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('componentes/{componente}', fn (ComponenteCurricular $componente) => $componente)
            ->bind('componente', $componenteParaEditar));

        $dadosCarga = ['carga_horaria' => '']; 
        $validatorCarga = Validator::make($dadosCarga, $request->rules());
        $this->assertTrue($validatorCarga->fails());
        $this->assertArrayHasKey('carga_horaria', $validatorCarga->errors()->toArray());
        $dadosStatus = ['status' => 'cancelado'];
        $validatorStatus = Validator::make($dadosStatus, $request->rules());
        $this->assertTrue($validatorStatus->fails());
        $this->assertArrayHasKey('status', $validatorStatus->errors()->toArray());
    }
}