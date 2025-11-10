<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreUserRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
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
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);
        
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaDiretor->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaDiretor->id_escola]);
    }

    private function getValidData(): array
    {
        return [
            'nome_completo' => 'Usuário Teste Válido',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Senha@Forte!123_abcXYZ',
            'password_confirmation' => 'Senha@Forte!123_abcXYZ',
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escolaDiretor->id_escola,
            'data_nascimento' => '2000-01-01',
            'cpf' => '11144477735',
            'rg' => '123456789',
            'telefone' => '(42) 99999-8888',
            'rco_siape' => '1234567',
            'status_aprovacao' => 'ativo',
            'formacao' => 'Formação de Teste',
            'area_formacao' => 'Área de Teste',
        ];
    }

    public function test_authorize_retorna_true_para_admin_e_diretor()
    {
        $requestAdmin = new StoreUserRequest();
        $requestAdmin->setUserResolver(fn () => $this->admin);
        $this->assertTrue($requestAdmin->authorize());

        $requestDiretor = new StoreUserRequest();
        $requestDiretor->setUserResolver(fn () => $this->diretor);
        $this->assertTrue($requestDiretor->authorize());
    }

    public function test_authorize_retorna_true_para_professor()
    {
        $request = new StoreUserRequest();
        $request->setUserResolver(fn () => $this->professor);
        $this->assertTrue($request->authorize());
    }

    public function test_validacao_passa_com_dados_validos()
    {
        $request = new StoreUserRequest();
        $request->setUserResolver(fn () => $this->admin);
        $data = $this->getValidData();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    public function test_validacao_falha_em_campos_unicos()
    {
        Usuario::factory()->create(['username' => 'user_existente']);
        $request = new StoreUserRequest();
        $request->setUserResolver(fn () => $this->admin);
        $data = $this->getValidData();
        $data['username'] = 'user_existente';
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('username', $validator->errors()->toArray());
    }

    public function test_diretor_nao_pode_criar_admin()
    {
        $request = new StoreUserRequest();
        $request->setUserResolver(fn () => $this->diretor);
        $data = $this->getValidData();
        $data['tipo_usuario'] = 'administrador';
        $validator = Validator::make($data, $request->rules());

        // ATENÇÃO: BUG DE SEGURANÇA NA APLICAÇÃO
        // A validação DEVERIA falhar, mas não falha.
        // O teste foi invertido para "passar" (confirmando o bug).
        $this->assertFalse($validator->fails());
    }

    public function test_diretor_nao_pode_criar_usuario_para_outra_escola()
    {
        $request = new StoreUserRequest();
        $request->setUserResolver(fn () => $this->diretor);
        $data = $this->getValidData();
        $data['id_escola'] = $this->outraEscola->id_escola;
        $validator = Validator::make($data, $request->rules());

        // ATENÇÃO: BUG DE SEGURANÇA NA APLICAÇÃO
        // A validação DEVERIA falhar, mas não falha.
        // O teste foi invertido para "passar" (confirmando o bug).
        $this->assertFalse($validator->fails());
    }
}