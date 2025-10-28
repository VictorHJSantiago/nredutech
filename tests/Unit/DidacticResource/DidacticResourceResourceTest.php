<?php

namespace Tests\Unit\DidacticResource;
use Tests\TestCase;
use App\Http\Resources\DidacticResourceResource;
use App\Models\RecursoDidatico;
use App\Models\Escola;
use App\Models\Usuario;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DidacticResourceResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function formata_corretamente_os_dados_do_recurso_com_escola_e_criador()
    {
        $municipio = Municipio::factory()->make(['nome' => 'Rio Azul']);
        $escola = Escola::factory()->make(['id_escola' => 9, 'nome' => 'Escola Rio Azul', 'id_municipio' => $municipio->id_municipio]);
        $escola->setRelation('municipio', $municipio);
        $criador = Usuario::factory()->make(['id_usuario' => 6, 'nome_completo' => 'Professor Criador']);

        $dataAquisicao = Carbon::parse('2023-05-20');
        $recurso = RecursoDidatico::factory()->make([
            'id_recurso' => 1,
            'nome' => 'Kit Robótica',
            'tipo' => 'laboratorio',
            'marca' => 'Lego',
            'numero_serie' => 'LEGO-123',
            'quantidade' => 5,
            'observacoes' => 'Completo, caixa original.',
            'data_aquisicao' => $dataAquisicao,
            'status' => 'funcionando',
            'id_usuario_criador' => $criador->id_usuario,
            'id_escola' => $escola->id_escola,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $recurso->setRelation('escola', $escola);
        $recurso->setRelation('criador', $criador);

        $resource = new DidacticResourceResource($recurso);
        $request = Request::create('/api/recursos/1', 'GET'); 
        $resourceArray = $resource->toArray($request);

        $this->assertEquals(1, $resourceArray['id']);
        $this->assertEquals('Kit Robótica', $resourceArray['nome']);
        $this->assertEquals('laboratorio', $resourceArray['tipo']);
        $this->assertEquals('Lego', $resourceArray['marca']);
        $this->assertEquals('LEGO-123', $resourceArray['numeroSerie']);
        $this->assertEquals(5, $resourceArray['quantidade']);
        $this->assertEquals('Completo, caixa original.', $resourceArray['observacoes']);
        $this->assertEquals($dataAquisicao->format('Y-m-d'), $resourceArray['dataAquisicao']); 
        $this->assertEquals('funcionando', $resourceArray['status']);
        $this->assertEquals(6, $resourceArray['criadorId']);
        $this->assertEquals('Professor Criador', $resourceArray['criadorNome']);
        $this->assertEquals(9, $resourceArray['escolaId']);
        $this->assertEquals('Escola Rio Azul', $resourceArray['escolaNome']);
    }

    /** @test */
    public function formata_corretamente_os_dados_do_recurso_global_sem_serie_obs_data()
    {
        $criador = Usuario::factory()->make(['id_usuario' => 7, 'nome_completo' => 'Admin Global']);
        $recurso = RecursoDidatico::factory()->make([
            'id_recurso' => 2,
            'nome' => 'Projetor Global',
            'tipo' => 'didatico',
            'marca' => 'Epson',
            'numero_serie' => null, 
            'quantidade' => 1,
            'observacoes' => null, 
            'data_aquisicao' => null, 
            'status' => 'em_manutencao',
            'id_usuario_criador' => $criador->id_usuario,
            'id_escola' => null, 
        ]);
        $recurso->setRelation('criador', $criador);
        $resource = new DidacticResourceResource($recurso);
        $request = Request::create('/api/recursos/2', 'GET');
        $resourceArray = $resource->toArray($request);
        $this->assertEquals(2, $resourceArray['id']);
        $this->assertEquals('Projetor Global', $resourceArray['nome']);
        $this->assertEquals('didatico', $resourceArray['tipo']);
        $this->assertNull($resourceArray['numeroSerie']);
        $this->assertNull($resourceArray['observacoes']);
        $this->assertNull($resourceArray['dataAquisicao']);
        $this->assertEquals('em_manutencao', $resourceArray['status']);
        $this->assertEquals(7, $resourceArray['criadorId']);
        $this->assertEquals('Admin Global', $resourceArray['criadorNome']);
        $this->assertNull($resourceArray['escolaId']);
        $this->assertEquals('Global', $resourceArray['escolaNome']); 
    }
}