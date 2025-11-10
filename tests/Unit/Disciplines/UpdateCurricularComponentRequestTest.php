<?php

namespace Tests\Unit\Disciplines;

use Tests\TestCase;
use App\Http\Requests\UpdateCurricularComponentRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use App\Models\ComponenteCurricular;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use PHPUnit\Framework\Attributes\Test;

class UpdateCurricularComponentRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $escolaDiretor;
    protected $outraEscola;
    protected $componenteDiretor;
    protected $componenteGlobal;
    protected $componenteOutraEscola;

    protected function setUp(): void
    {
        parent::setUp();
        
        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        
        $this->escolaDiretor = Escola::create([
            'nome' => 'Escola Diretor',
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
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaDiretor->id_escola]);

        $this->componenteDiretor = ComponenteCurricular::create(['nome' => 'Componente Diretor', 'id_escola' => $this->escolaDiretor->id_escola, 'carga_horaria' => 60]);
        $this->componenteGlobal = ComponenteCurricular::create(['nome' => 'Componente Global', 'id_escola' => null, 'carga_horaria' => 60]);
        $this->componenteOutraEscola = ComponenteCurricular::create(['nome' => 'Componente Outra', 'id_escola' => $this->outraEscola->id_escola, 'carga_horaria' => 60]);
    }

    private function createRequestFor(Usuario $user, ComponenteCurricular $componente): UpdateCurricularComponentRequest
    {
        $this->actingAs($user);
        $request = new UpdateCurricularComponentRequest();
        $request->setUserResolver(fn () => $user);
        
        $request->setRouteResolver(function () use ($componente) {
            $route = new Route('PUT', 'componentes/{componente}', []);
            $route->bind('componente', $componente);
            return $route;
        });
        
        return $request;
    }

    #[Test]
    public function authorize_returns_true_for_admin_and_diretor()
    {
        $requestAdmin = $this->createRequestFor($this->admin, $this->componenteGlobal);
        $this->assertTrue($requestAdmin->authorize());

        $requestDiretor = $this->createRequestFor($this->diretor, $this->componenteDiretor);
        $this->assertTrue($requestDiretor->authorize());
    }

    #[Test]
    public function validation_passes_when_nome_is_unchanged()
    {
        $request = $this->createRequestFor($this->admin, $this->componenteGlobal);
        $dados = ['nome' => $this->componenteGlobal->nome];
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function validation_fails_on_duplicate_nome_for_same_school()
    {
        ComponenteCurricular::create(['nome' => 'Nome Duplicado', 'id_escola' => $this->escolaDiretor->id_escola, 'carga_horaria' => '60']);
        
        $request = $this->createRequestFor($this->diretor, $this->componenteDiretor);
        $dados = ['nome' => 'Nome Duplicado'];
        $validator = Validator::make($dados, $request->rules());
        
        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    #[Test]
    public function validation_passes_on_duplicate_nome_for_different_school()
    {
        ComponenteCurricular::create(['nome' => 'Nome Duplicado', 'id_escola' => $this->outraEscola->id_escola, 'carga_horaria' => '60']);
        
        $request = $this->createRequestFor($this->diretor, $this->componenteDiretor);
        $dados = ['nome' => 'Nome Duplicado'];
        $validator = Validator::make($dados, $request->rules());
        
        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    #[Test]
    public function diretor_cannot_update_to_other_school()
    {
        $request = $this->createRequestFor($this->diretor, $this->componenteDiretor);
        $dados = ['id_escola' => $this->outraEscola->id_escola];
        $validator = Validator::make($dados, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function diretor_cannot_update_to_global()
    {
        $request = $this->createRequestFor($this->diretor, $this->componenteDiretor);
        $dados = ['id_escola' => null];
        $validator = Validator::make($dados, $request->rules());
        
        $this->assertFalse($validator->fails());
    }
}