<?php

namespace Tests\Unit\SchoolClass; 

use Tests\TestCase;
use App\Http\Resources\SchoolClassResource;
use App\Models\Turma;
use App\Models\Escola;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SchoolClassResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function formata_corretamente_os_dados_da_turma()
    {
        $municipio = Municipio::factory()->make(['nome' => 'Imbituva']);
        $escola = Escola::factory()->make([
            'id_escola' => 5,
            'nome' => 'Escola Estadual Central',
            'id_municipio' => $municipio->id_municipio
        ]);
        $escola->setRelation('municipio', $municipio);

        $turma = Turma::factory()->make([
            'id_turma' => 1,
            'serie' => '3º Ano A',
            'turno' => 'manha',
            'ano_letivo' => 2025,
            'nivel_escolaridade' => 'medio',
            'id_escola' => $escola->id_escola,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $turma->setRelation('escola', $escola); 

        $resource = new SchoolClassResource($turma);
        $request = Request::create('/api/turmas/1', 'GET'); 
        $resourceArray = $resource->toArray($request);

        $this->assertEquals(1, $resourceArray['id']);
        $this->assertEquals('3º Ano A', $resourceArray['serie']);
        $this->assertEquals('manha', $resourceArray['turno']);
        $this->assertEquals(2025, $resourceArray['anoLetivo']);
        $this->assertEquals('medio', $resourceArray['nivelEscolaridade']);
        $this->assertEquals(5, $resourceArray['escolaId']);
        $this->assertEquals('Escola Estadual Central', $resourceArray['escolaNome']); 
        // $this->assertArrayNotHasKey('created_at', $resourceArray);
    }
}