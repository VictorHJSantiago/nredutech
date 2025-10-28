<?php

namespace Tests\Unit\School;

use Tests\TestCase;
use App\Http\Resources\SchoolResource;
use App\Models\Escola;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SchoolResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function formata_corretamente_os_dados_da_escola()
    {
        $municipio = Municipio::factory()->make(['id_municipio' => 10, 'nome' => 'Irati']); 
        $escola = Escola::factory()->make([
            'id_escola' => 1,
            'nome' => 'Escola Modelo',
            'nivel_ensino' => 'medio',
            'localizacao' => 'urbana',
            'id_municipio' => $municipio->id_municipio
        ]);
        $escola->setRelation('municipio', $municipio);

        $resource = new SchoolResource($escola);
        $request = Request::create('/api/escolas/1', 'GET');
        $resourceArray = $resource->toArray($request);

        $this->assertEquals(1, $resourceArray['id']);
        $this->assertEquals('Escola Modelo', $resourceArray['nome']);
        $this->assertEquals('medio', $resourceArray['nivelEnsino']);
        $this->assertEquals('urbana', $resourceArray['localizacao']);
        $this->assertEquals(10, $resourceArray['municipioId']);
        $this->assertEquals('Irati', $resourceArray['municipioNome']); 
    }
}