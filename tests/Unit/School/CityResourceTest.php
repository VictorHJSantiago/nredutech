<?php

namespace Tests\Unit\School;

use Tests\TestCase;
use App\Http\Resources\CityResource;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CityResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function formata_corretamente_os_dados_do_municipio()
    {
        $municipio = Municipio::factory()->make([
            'id_municipio' => 5,
            'nome' => 'Rebouças'
        ]);

        $resource = new CityResource($municipio);
        $request = Request::create('/api/municipios/5', 'GET'); 
        $resourceArray = $resource->toArray($request);

        $this->assertEquals(5, $resourceArray['id']);
        $this->assertEquals('Rebouças', $resourceArray['nome']);
    }
}