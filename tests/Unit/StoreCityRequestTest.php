<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\StoreCityRequest;
use Illuminate\Support\Facades\Validator;

class StoreCityRequestTest extends TestCase
{
    /**
     * @test
     */
    public function nome_do_municipio_e_obrigatorio()
    {
        $request = new StoreCityRequest();
        $validator = Validator::make([], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
    }

    /**
     * @test
     */
    public function validacao_passa_com_nome_valido()
    {
        $request = new StoreCityRequest();
        $validator = Validator::make(['nome' => 'Irati'], $request->rules());

        $this->assertFalse($validator->fails());
    }

    /**
     * @test
     */
    public function nome_do_municipio_nao_deve_exceder_maximo()
    {
        $request = new StoreCityRequest();
        $longName = str_repeat('a', 256); 
        $validator = Validator::make(['nome' => $longName], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
    }
}
