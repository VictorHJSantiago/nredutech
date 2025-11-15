<?php

namespace Tests\Unit\Disciplines;

use Tests\TestCase;
use App\Http\Resources\CurricularComponentResource;
use App\Models\ComponenteCurricular;
use App\Models\Escola;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CurricularComponentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected $escola;

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
    }

    #[Test]
    public function testa_recurso_de_componente_curricular_transforma_corretamente_com_escola()
    {
        $componente = ComponenteCurricular::create([
            'nome' => 'Matemática',
            'descricao' => 'Descricao Matematica',
            'carga_horaria' => 80,
            'status' => 'aprovado',
            'id_escola' => $this->escola->id_escola
        ]);
        $componente->load('escola');
        
        $request = Request::create('/api/componentes/1', 'GET');
        $resource = new CurricularComponentResource($componente);
        $resourceArray = $resource->toArray($request);

        $expectedArray = [
            'id' => $componente->id_componente,
            'nome' => 'Matemática',
            'descricao' => 'Descricao Matematica',
            'cargaHoraria' => 80,
            'status' => 'aprovado',
            'ofertas' => $resourceArray['ofertas'], 
        ];

        $this->assertEquals($expectedArray, $resourceArray);
    }

    #[Test]
    public function testa_recurso_de_componente_curricular_transforma_corretamente_quando_global()
    {
        $componente = ComponenteCurricular::create([
            'nome' => 'Matemática Global',
            'descricao' => 'Descricao Global',
            'carga_horaria' => 120,
            'status' => 'pendente',
            'id_escola' => null
        ]);
        
        $request = Request::create('/api/componentes/2', 'GET');
        $resource = new CurricularComponentResource($componente);
        $resourceArray = $resource->toArray($request);

        $expectedArray = [
            'id' => $componente->id_componente,
            'nome' => 'Matemática Global',
            'descricao' => 'Descricao Global',
            'cargaHoraria' => 120,
            'status' => 'pendente',
            'ofertas' => $resourceArray['ofertas'], 
        ];
        
        $this->assertEquals($expectedArray, $resourceArray);
    }
}