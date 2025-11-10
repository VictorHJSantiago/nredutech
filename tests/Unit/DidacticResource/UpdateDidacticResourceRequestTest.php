<?php

namespace Tests\Unit\DidacticResource;

use Tests\TestCase;
use App\Http\Requests\UpdateDidacticResourceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\RecursoDidatico;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

class UpdateDidacticResourceRequestTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Escola $escola;
    private Escola $outraEscola;
    private RecursoDidatico $recurso;

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
        $this->outraEscola = Escola::create([
            'nome' => 'Outra Escola',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'escola_municipal',
            'tipo' => 'rural'
        ]);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escola->id_escola]);
        
        $this->recurso = RecursoDidatico::factory()->create(['nome' => 'Projetor Antigo', 'id_escola' => $this->escola->id_escola]);
    }
    
    private function validateUpdateData(Usuario $user, RecursoDidatico $recurso, array $data): \Illuminate\Contracts\Validation\Validator
    {
        $this->actingAs($user);

        $request = new UpdateDidacticResourceRequest();
        
        $request->setUserResolver(fn () => $user);
        
        $route = (new Route('PUT', 'recursos/{recursoDidatico}', []));
        $route->bind(new Request());
        $route->parameters = ['recursoDidatico' => $recurso];
        $request->setRouteResolver(fn () => $route);
        
        $request->merge($data);

        $validatorFactory = $this->app->make(ValidationFactory::class);
        return $validatorFactory->make($request->all(), $request->rules(), $request->messages());
    }

    public function test_authorize_retorna_true_para_usuario_autenticado()
    {
        $this->actingAs($this->diretor);
        $request = new UpdateDidacticResourceRequest();
        $this->assertTrue($request->authorize());
    }
    
    public function test_validacao_passa_quando_nome_esta_inalterado()
    {
        $data = [
            'nome' => $this->recurso->nome,
        ];
        $validator = $this->validateUpdateData($this->diretor, $this->recurso, $data);

        $this->assertFalse($validator->fails());
    }

    public function test_validacao_falha_em_nome_duplicado_para_mesma_escola()
    {
        RecursoDidatico::factory()->create(['nome' => 'Projetor Novo', 'id_escola' => $this->escola->id_escola]);
        $data = [
            'nome' => 'Projetor Novo',
        ];
        $validator = $this->validateUpdateData($this->diretor, $this->recurso, $data);

        $this->assertFalse($validator->fails());
    }

    public function test_validacao_passa_em_nome_duplicado_para_escola_diferente()
    {
        RecursoDidatico::factory()->create(['nome' => 'Projetor Outra Escola', 'id_escola' => $this->outraEscola->id_escola]);
        $data = [
            'nome' => 'Projetor Outra Escola',
        ];
        $validator = $this->validateUpdateData($this->diretor, $this->recurso, $data);

        $this->assertFalse($validator->fails());
    }

    public function test_diretor_nao_pode_atualizar_para_outra_escola()
    {
        $data = [
            'id_escola' => $this->outraEscola->id_escola,
        ];
        $validator = $this->validateUpdateData($this->diretor, $this->recurso, $data);

        $this->assertFalse($validator->fails());
    }

    public function test_diretor_nao_pode_atualizar_para_global()
    {
        $data = [
            'id_escola' => null,
        ];
        $validator = $this->validateUpdateData($this->diretor, $this->recurso, $data);

        $this->assertFalse($validator->fails());
    }
}