<?php

namespace Tests\Unit\School;

use Tests\TestCase;
use App\Http\Requests\UpdateCityRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Municipio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Routing\Route; 

class UpdateCityRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $request;
    protected $municipio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new UpdateCityRequest();
        
        $this->municipio = Municipio::create(['nome' => 'Municipio Original', 'estado' => 'PR']);
        
        $this->request->setRouteResolver(function () {
            $route = new Route('PUT', 'municipios/{municipio}', []);
            $route->bind('municipio', $this->municipio);
            return $route;
        });
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
    public function validacao_passa_com_mesmo_nome()
    {
        $data = ['nome' => $this->municipio->nome];
        $validator = Validator::make($data, $this->request->rules());
        
        $this->assertFalse($validator->fails());
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