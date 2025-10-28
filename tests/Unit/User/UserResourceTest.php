<?php

namespace Tests\Unit\User;
use Tests\TestCase;
use App\Http\Resources\UserResource;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function formata_corretamente_os_dados_do_usuario()
    {
        $usuario = Usuario::factory()->make([
            'id_usuario' => 123,
            'nome_completo' => 'João da Silva',
            'username' => 'joao.silva',
            'email' => 'joao@example.com',
            'status_aprovacao' => 'ativo',
            'tipo_usuario' => 'professor'
        ]);

        $resource = new UserResource($usuario);
        $request = Request::create('/api/usuarios/123', 'GET');
        $resourceArray = $resource->toArray($request);

        $this->assertEquals(123, $resourceArray['id']);
        $this->assertEquals('João da Silva', $resourceArray['nomeCompleto']);
        $this->assertEquals('joao.silva', $resourceArray['username']);
        $this->assertEquals('ativo', $resourceArray['status']);
        $this->assertEquals('professor', $resourceArray['tipo']);
        $this->assertArrayNotHasKey('password', $resourceArray); 
    }
}