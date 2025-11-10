<?php

namespace Tests\Unit\School;

use Tests\TestCase;
use App\Http\Requests\StoreCityRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class StoreCityRequestTest extends TestCase
{
    protected $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreCityRequest();
    }

    #[Test]
    public function autorizacao_retorna_verdadeiro()
    {
        $this->assertTrue($this->request->authorize());
    }

    #[Test]
    public function validacao_passa_com_dados_validos()
    {
        $data = ['nome' => 'Irati'];
        $validator = Validator::make($data, $this->request->rules());
        
        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function validacao_falha_com_nome_ausente()
    {
        $data = ['nome' => ''];
        $validator = Validator::make($data, $this->request->rules());
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
    }

    #[Test]
    public function validacao_falha_com_nome_muito_longo()
    {
        $data = ['nome' => str_repeat('a', 256)];
        $validator = Validator::make($data, $this->request->rules());
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
    }
}