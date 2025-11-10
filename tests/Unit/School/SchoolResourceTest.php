<?php

namespace Tests\Unit\School;

use Tests\TestCase;
use App\Http\Resources\SchoolResource;
use App\Models\Escola;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class SchoolResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function formata_corretamente_os_dados_da_escola()
    {
        $municipio = Municipio::create(['nome' => 'Irati', 'estado' => 'PR']); 
        
        $escola = Escola::create([
            'nome' => 'Escola Modelo',
            'nivel_ensino' => 'escola_tecnica',
            'tipo' => 'urbana', 
            'id_municipio' => $municipio->id_municipio
        ]);
        $escola->load('municipio'); 

        $resource = new SchoolResource($escola);
        $request = Request::create('/api/escolas/1', 'GET');
        $resourceArray = $resource->toArray($request);

        $this->assertEquals($escola->id_escola, $resourceArray['id']);
        $this->assertEquals('Escola Modelo', $resourceArray['nome']);
        
        $this->assertEquals('Irati', $resourceArray['municipio']['nome']); 
    }
}