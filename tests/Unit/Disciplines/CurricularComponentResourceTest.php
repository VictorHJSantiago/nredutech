<?php

namespace Tests\Unit\Disciplines; 

use Tests\TestCase;
use App\Http\Resources\CurricularComponentResource;
use App\Models\ComponenteCurricular;
use App\Models\Escola;
use App\Models\Usuario;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CurricularComponentResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function formata_corretamente_os_dados_do_componente_com_escola()
    {
        $municipio = Municipio::factory()->make(['nome' => 'Prudentópolis']);
        $escola = Escola::factory()->make(['id_escola' => 8, 'nome' => 'Escola da Vila', 'id_municipio' => $municipio->id_municipio]);
        $escola->setRelation('municipio', $municipio);
        $criador = Usuario::factory()->make(['id_usuario' => 4, 'nome_completo' => 'Criador Teste']);

        $componente = ComponenteCurricular::factory()->make([
            'id_componente' => 1,
            'nome' => 'História Antiga',
            'descricao' => 'Estudo das civilizações passadas.',
            'carga_horaria' => '80h',
            'status' => 'aprovado',
            'id_usuario_criador' => $criador->id_usuario,
            'id_escola' => $escola->id_escola,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $componente->setRelation('escola', $escola);
        $componente->setRelation('criador', $criador);

        $resource = new CurricularComponentResource($componente);
        $request = Request::create('/api/componentes/1', 'GET'); 
        $resourceArray = $resource->toArray($request);

        $this->assertEquals(1, $resourceArray['id']);
        $this->assertEquals('História Antiga', $resourceArray['nome']);
        $this->assertEquals('Estudo das civilizações passadas.', $resourceArray['descricao']);
        $this->assertEquals('80h', $resourceArray['cargaHoraria']);
        $this->assertEquals('aprovado', $resourceArray['status']);
        $this->assertEquals(4, $resourceArray['criadorId']);
        $this->assertEquals('Criador Teste', $resourceArray['criadorNome']);
        $this->assertEquals(8, $resourceArray['escolaId']);
        $this->assertEquals('Escola da Vila', $resourceArray['escolaNome']);
    }

    /** @test */
    public function formata_corretamente_os_dados_do_componente_global()
    {
        $criador = Usuario::factory()->make(['id_usuario' => 5, 'nome_completo' => 'Admin Criador']);
        $componente = ComponenteCurricular::factory()->make([
            'id_componente' => 2,
            'nome' => 'Matemática Básica Global',
            'carga_horaria' => '60h',
            'status' => 'pendente',
            'id_usuario_criador' => $criador->id_usuario,
            'id_escola' => null, 
        ]);
        $componente->setRelation('criador', $criador);
        $resource = new CurricularComponentResource($componente);
        $request = Request::create('/api/componentes/2', 'GET');
        $resourceArray = $resource->toArray($request);

        $this->assertEquals(2, $resourceArray['id']);
        $this->assertEquals('Matemática Básica Global', $resourceArray['nome']);
        $this->assertEquals('pendente', $resourceArray['status']);
        $this->assertEquals(5, $resourceArray['criadorId']);
        $this->assertEquals('Admin Criador', $resourceArray['criadorNome']);
        $this->assertNull($resourceArray['escolaId']); 
        $this->assertEquals('Global', $resourceArray['escolaNome']); 
    }
}