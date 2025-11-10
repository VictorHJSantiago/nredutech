<?php

namespace Tests\Unit\SchoolClass; 

use Tests\TestCase;
use App\Http\Resources\SchoolClassResource;
use App\Http\Resources\SchoolResource;
use App\Models\Turma;
use App\Models\Escola;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class SchoolClassResourceTest extends TestCase
{
    use RefreshDatabase;

    protected $escola;
    protected $municipio;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->municipio = Municipio::create(['nome' => 'Imbituva']);
        
        $this->escola = Escola::create([
            'nome' => 'Escola Estadual Central',
            'id_municipio' => $this->municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);
    }

    #[Test]
    public function formata_corretamente_os_dados_da_turma()
    {
        $turma = Turma::factory()->make([
            'id_turma' => 1,
            'serie' => '3º Ano A',
            'turno' => 'manha',
            'ano_letivo' => 2025,
            'nivel_escolaridade' => 'medio',
            'id_escola' => $this->escola->id_escola,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->escola->setRelation('municipio', $this->municipio);
        $turma->setRelation('escola', $this->escola); 

        $resource = new SchoolClassResource($turma);
        $request = Request::create('/api/turmas/1', 'GET'); 
        $resourceArray = $resource->toArray($request);

        $this->assertEquals(1, $resourceArray['id']);
        $this->assertEquals('3º Ano A', $resourceArray['serie']);
        $this->assertEquals('manha', $resourceArray['turno']);
        $this->assertEquals(2025, $resourceArray['anoLetivo']);
        $this->assertEquals('medio', $resourceArray['nivelEscolaridade']);
        
        $this->assertArrayHasKey('escola', $resourceArray);
        $this->assertInstanceOf(SchoolResource::class, $resourceArray['escola']);
        
        $escolaArray = $resourceArray['escola']->toArray($request);
        
        $this->assertEquals($this->escola->id_escola, $escolaArray['id']); 

        $this->assertEquals('Escola Estadual Central', $escolaArray['nome']);
    }
}