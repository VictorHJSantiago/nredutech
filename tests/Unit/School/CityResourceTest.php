<?php

namespace Tests\Unit\School;

use Tests\TestCase;
use App\Http\Resources\CityResource;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CityResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function formata_corretamente_os_dados_do_municipio()
    {
        $municipio = Municipio::create([
            'nome' => 'Rebouças'
        ]);
        
        $municipio->tipo = null; 

        $resource = new CityResource($municipio);
        $request = Request::create('/api/municipios/1', 'GET'); 
        $resourceArray = $resource->toArray($request);

        $this->assertEquals($municipio->id_municipio, $resourceArray['id']);
        $this->assertEquals('Rebouças', $resourceArray['nome']);
        $this->assertArrayHasKey('tipo', $resourceArray);
        $this->assertNull($resourceArray['tipo']);
    }
}