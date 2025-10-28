<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Rules\RgValido;
use Illuminate\Support\Facades\Validator;

class RgValidoTest extends TestCase
{
    /**
     * @test
     * @dataProvider 
     */
    public function validacao_passa_para_rgs_validos($rg)
    {
        $rule = ['rg' => new RgValido];
        $validator = Validator::make(['rg' => $rg], $rule);
        $this->assertFalse($validator->fails());
    }

    /**
     * @test
     * @dataProvider 
     */
    public function validacao_falha_para_rgs_invalidos($rg)
    {
        $rule = ['rg' => new RgValido];
        $validator = Validator::make(['rg' => $rg], $rule);
        $this->assertTrue($validator->fails());
    }

    public static function rgValidosProvider(): array
    {
        return [
            'com formatacao' => ['12.345.678-9'],
            'sem formatacao' => ['123456789'],
            '7 digitos' => ['7654321'],
        ];
    }
    public static function rgInvalidosProvider(): array
    {
        return [
            'vazio' => [''],
            'curto' => ['12345'],
            'longo' => ['12345678901'],
            'repetido' => ['11111111'],
        ];
    }
}