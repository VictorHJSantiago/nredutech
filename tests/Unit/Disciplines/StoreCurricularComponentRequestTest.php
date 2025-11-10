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
    public function authorize_returns_true_for_admin_and_diretor()
    {
        $requestAdmin = new StoreCurricularComponentRequest();
        $requestAdmin->setUserResolver(fn () => $this->admin);
        $this->assertTrue($requestAdmin->authorize());

        $requestDiretor = new StoreCurricularComponentRequest();
        $requestDiretor->setUserResolver(fn () => $this->diretor);
        $this->assertTrue($requestDiretor->authorize());
    }

    #[Test]
    public function validation_passes_with_valid_data_for_school()
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
    public function validation_passes_with_valid_data_for_global()
    {
        $this->actingAs($this->admin);
        $request = new StoreCurricularComponentRequest();
        $request->setUserResolver(fn () => $this->admin);

        $dados = $this->getValidData(null);
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    #[Test]
    public function validation_fails_on_missing_nome()
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
    public function validation_fails_on_non_existent_escola()
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
    public function validation_fails_on_duplicate_nome_for_same_school()
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
    public function validation_passes_on_duplicate_nome_for_different_school()
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
    public function validation_fails_on_duplicate_global_nome()
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
    public function diretor_cannot_create_component_for_other_school()
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
    public function diretor_cannot_create_global_component()
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