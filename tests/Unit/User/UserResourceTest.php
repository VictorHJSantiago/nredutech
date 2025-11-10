<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use App\Http\Resources\UserResource;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class UserResourceTest extends TestCase
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

    public function test_recurso_de_usuario_transforma_corretamente_com_escola()
    {
        $user = Usuario::factory()->create([
            'nome_completo' => 'Usuário Teste',
            'username' => 'testeuser',
            'email' => 'teste@example.com',
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escola->id_escola,
        ]);
        $user->load('escola'); 

        $resource = new UserResource($user);
        $request = Request::create('/api/users/1', 'GET');
        $resourceArray = $resource->toArray($request);

        $this->assertEquals($user->id_usuario, $resourceArray['id']);
        $this->assertEquals('Usuário Teste', $resourceArray['nomeCompleto']);
        $this->assertEquals('testeuser', $resourceArray['username']);
        $this->assertEquals('teste@example.com', $resourceArray['email']);
        $this->assertEquals('professor', $resourceArray['tipo']);
        $this->assertEquals('Escola Teste', $resourceArray['escola']->nome);
    }

    public function test_recurso_de_usuario_transforma_corretamente_sem_escola()
    {
        $user = Usuario::factory()->create([
            'nome_completo' => 'Admin Teste',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'tipo_usuario' => 'administrador',
            'id_escola' => null,
        ]);
        
        $user->load('escola');

        $resource = new UserResource($user);
        $request = Request::create('/api/users/2', 'GET');
        $resourceArray = $resource->toArray($request);

        $this->assertEquals($user->id_usuario, $resourceArray['id']);
        $this->assertEquals('Admin Teste', $resourceArray['nomeCompleto']);
        $this->assertEquals('administrador', $resourceArray['tipo']);
        
        // O resource 'escola' é um objeto (SchoolResource) com um 'resource' interno nulo
        // A asserção correta é verificar o 'resource' interno.
        $this->assertNull($resourceArray['escola']->resource);
    }
}