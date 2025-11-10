<?php

namespace Tests\Unit\Rules;

use Tests\TestCase;
use App\Rules\RgValido;

class RgValidoTest extends TestCase
{
    private $rule;
    private $closure;
    private $failed = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new RgValido();
        $this->failed = false;
        $this->closure = function ($message) {
            $this->failed = $message;
        };
    }

    public function test_rg_valido_numerico_passa()
    {
        $this->rule->validate('rg', '12.345.678-9', $this->closure);
        $this->assertFalse($this->failed);
    }

    public function test_rg_valido_com_x_passa()
    {
        $this->rule->validate('rg', '12.345.678-X', $this->closure);
        $this->assertFalse($this->failed);
    }

    public function test_rg_com_comprimento_curto_passa()
    {
        $this->rule->validate('rg', '12.345.67-8', $this->closure);
        $this->assertFalse($this->failed);
    }

    public function test_rg_invalido_falha_com_comprimento_longo()
    {
        $this->rule->validate('rg', '12.345.678-90', $this->closure);
        $this->assertNotFalse($this->failed);
        $this->assertEquals('O campo :attribute não é um RG válido.', $this->failed);
    }
}