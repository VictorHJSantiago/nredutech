<?php

namespace Tests\Unit\Disciplines;

use Tests\TestCase;
use App\Http\Requests\StoreCurricularComponentRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use App\Models\ComponenteCurricular;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class StoreCurricularComponentRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $escolaDiretor;
    protected $outraEscola;

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
    }

    private function getValidData($escolaId = null): array
    {
        return [
            'nome' => 'Nova Disciplina',
            'descricao' => 'Descrição da disciplina',
            'carga_horaria' => '60',
            'status' => 'aprovado',
            'id_escola' => $escolaId,
        ];
    }

    #[Test]
    public function testa_autorizacao_retorna_verdadeiro_para_admin_e_diretor()
    {
        $requestAdmin = new StoreCurricularComponentRequest();
        $requestAdmin->setUserResolver(fn () => $this->admin);
        $this->assertTrue($requestAdmin->authorize());

        $requestDiretor = new StoreCurricularComponentRequest();
        $requestDiretor->setUserResolver(fn () => $this->diretor);
        $this->assertTrue($requestDiretor->authorize());
    }

    #[Test]
    public function testa_validacao_passa_com_dados_validos_para_escola()
    {
        $this->actingAs($this->diretor);
        $request = new StoreCurricularComponentRequest();
        $request->setUserResolver(fn () => $this->diretor);
        
        $dados = $this->getValidData($this->escolaDiretor->id_escola);
        unset($dados['status']);
        
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    #[Test]
    public function testa_validacao_passa_com_dados_validos_para_global()
    {
        $this->actingAs($this->admin);
        $request = new StoreCurricularComponentRequest();
        $request->setUserResolver(fn () => $this->admin);

        $dados = $this->getValidData(null);
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    #[Test]
    public function testa_validacao_falha_com_nome_ausente()
    {
        $this->actingAs($this->admin);
        $request = new StoreCurricularComponentRequest();
        $request->setUserResolver(fn () => $this->admin);

        $dados = $this->getValidData();
        unset($dados['nome']);
        $validator = Validator::make($dados, $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
    }

    #[Test]
    public function testa_validacao_falha_com_escola_inexistente()
    {
        $this->actingAs($this->admin);
        $request = new StoreCurricularComponentRequest();
        $request->setUserResolver(fn () => $this->admin);

        $dados = $this->getValidData(999);
        $validator = Validator::make($dados, $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_escola', $validator->errors()->toArray());
    }

    #[Test]
    public function testa_validacao_falha_com_nome_duplicado_para_mesma_escola()
    {
        ComponenteCurricular::create(array_merge($this->getValidData($this->escolaDiretor->id_escola), ['nome' => 'Nome Duplicado']));
        
        $this->actingAs($this->diretor);
        $request = new StoreCurricularComponentRequest();
        $request->setUserResolver(fn () => $this->diretor);

        $dados = $this->getValidData($this->escolaDiretor->id_escola);
        unset($dados['status']); 
        $dados['nome'] = 'Nome Duplicado';
        
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function testa_validacao_passa_com_nome_duplicado_para_escola_diferente()
    {
        ComponenteCurricular::create(array_merge($this->getValidData($this->outraEscola->id_escola), ['nome' => 'Nome Duplicado']));
        
        $this->actingAs($this->diretor);
        $request = new StoreCurricularComponentRequest();
        $request->setUserResolver(fn () => $this->diretor);

        $dados = $this->getValidData($this->escolaDiretor->id_escola);
        unset($dados['status']);
        $dados['nome'] = 'Nome Duplicado';

        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    #[Test]
    public function testa_validacao_falha_com_nome_global_duplicado()
    {
        ComponenteCurricular::create(array_merge($this->getValidData(null), ['nome' => 'Nome Global Duplicado']));
        
        $this->actingAs($this->admin);
        $request = new StoreCurricularComponentRequest();
        $request->setUserResolver(fn () => $this->admin);

        $dados = $this->getValidData(null);
        $dados['nome'] = 'Nome Global Duplicado';
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function testa_diretor_nao_pode_criar_componente_para_outra_escola()
    {
        $this->actingAs($this->diretor);
        $request = new StoreCurricularComponentRequest();
        $request->setUserResolver(fn () => $this->diretor);
        
        $dados = $this->getValidData($this->outraEscola->id_escola);
        unset($dados['status']);
        
        $validator = Validator::make($dados, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function testa_diretor_nao_pode_criar_componente_global()
    {
        $this->actingAs($this->diretor);
        $request = new StoreCurricularComponentRequest();
        $request->setUserResolver(fn () => $this->diretor);
        
        $dados = $this->getValidData(null);
        unset($dados['status']);

        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails());
    }
}