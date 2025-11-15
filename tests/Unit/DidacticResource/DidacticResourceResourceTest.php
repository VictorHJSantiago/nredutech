<?php

namespace Tests\Unit\DidacticResource;

use Tests\TestCase;
use App\Models\RecursoDidatico;
use App\Models\Escola;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Http\Resources\DidacticResourceResource;
use App\Http\Resources\SchoolResource;
use App\Http\Resources\UserResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class DidacticResourceResourceTest extends TestCase
{
    use RefreshDatabase;

    private Escola $escola;
    private Usuario $criador;

    protected function setUp(): void
    {
        parent::setUp();
        
        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $this->escola = Escola::create([
            'nome' => 'Escola Teste',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);
        
        $this->criador = Usuario::factory()->create();
    }

    public function teste_resource_de_recurso_didatico_transforma_corretamente_para_recurso_de_escola()
    {
        $recurso = RecursoDidatico::factory()->create([
            'nome' => 'Projetor Sala 1',
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $this->criador->id_usuario,
        ]);
        $recurso->load('escola', 'criador');

        $resource = new DidacticResourceResource($recurso);
        $resourceArray = $resource->toArray(new Request());

        $this->assertEquals('Projetor Sala 1', $resourceArray['nome']);
        $this->assertArrayNotHasKey('escola', $resourceArray);
        $this->assertArrayNotHasKey('criador', $resourceArray);
    }

    public function teste_resource_de_recurso_didatico_transforma_corretamente_para_recurso_global()
    {
        $recurso = RecursoDidatico::factory()->create([
            'nome' => 'Projetor Global',
            'id_escola' => null,
            'id_usuario_criador' => $this->criador->id_usuario,
        ]);
        $recurso->load('escola', 'criador');

        $resource = new DidacticResourceResource($recurso);
        $resourceArray = $resource->toArray(new Request());

        $this->assertArrayNotHasKey('escola', $resourceArray);
        $this->assertEquals('Projetor Global', $resourceArray['nome']);
        $this->assertArrayNotHasKey('criador', $resourceArray);
    }
}