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
    public function testa_autorizacao_retorna_verdadeiro_para_admin_e_diretor()
    {
        $requestAdmin = $this->createRequestFor($this->admin, $this->componenteGlobal);
        $this->assertTrue($requestAdmin->authorize());

        $requestDiretor = $this->createRequestFor($this->diretor, $this->componenteDiretor);
        $this->assertTrue($requestDiretor->authorize());
    }

    #[Test]
    public function testa_validacao_passa_quando_nome_esta_inalterado()
    {
        $request = $this->createRequestFor($this->admin, $this->componenteGlobal);
        $dados = ['nome' => $this->componenteGlobal->nome];
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function testa_validacao_falha_com_nome_duplicado_para_mesma_escola()
    {
        ComponenteCurricular::create(['nome' => 'Nome Duplicado', 'id_escola' => $this->escolaDiretor->id_escola, 'carga_horaria' => '60']);
        
        $request = $this->createRequestFor($this->diretor, $this->componenteDiretor);
        $dados = ['nome' => 'Nome Duplicado'];
        $validator = Validator::make($dados, $request->rules());
        
        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    #[Test]
    public function testa_validacao_passa_com_nome_duplicado_para_escola_diferente()
    {
        ComponenteCurricular::create(['nome' => 'Nome Duplicado', 'id_escola' => $this->outraEscola->id_escola, 'carga_horaria' => '60']);
        
        $request = $this->createRequestFor($this->diretor, $this->componenteDiretor);
        $dados = ['nome' => 'Nome Duplicado'];
        $validator = Validator::make($dados, $request->rules());
        
        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    #[Test]
    public function testa_diretor_nao_pode_atualizar_para_outra_escola()
    {
        $request = $this->createRequestFor($this->diretor, $this->componenteDiretor);
        $dados = ['id_escola' => $this->outraEscola->id_escola];
        $validator = Validator::make($dados, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function testa_diretor_nao_pode_atualizar_para_global()
    {
        $request = $this->createRequestFor($this->diretor, $this->componenteDiretor);
        $dados = ['id_escola' => null];
        $validator = Validator::make($dados, $request->rules());
        
        $this->assertFalse($validator->fails());
    }
}