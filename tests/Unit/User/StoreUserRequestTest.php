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

    protected $escola;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $this->escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
    }

    /**
     * @test
     * @dataProvider 
     */
    public function campos_obrigatorios_basicos_falham_se_ausentes($campo, $valorAusente)
    {
        $request = new StoreUserRequest();
        $dados = $this->getValidData();
        $dados[$campo] = $valorAusente;

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($campo, $validator->errors()->toArray());
    }

    /** @test */
    public function validacao_passa_com_dados_validos_para_professor()
    {
        $request = new StoreUserRequest();
        $dados = $this->getValidData(); 
        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails());
    }

     /** @test */
    public function validacao_passa_com_dados_validos_para_admin()
    {
        $request = new StoreUserRequest();
        $dados = $this->getValidData();
        $dados['tipo_usuario'] = 'administrador';
        $dados['id_escola'] = null; 
        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails());
    }


    /**
     * @test
     */
    public function regras_tipo_usuario_escola_sao_aplicadas()
    {
        $request = new StoreUserRequest();
        $dadosProfSemEscola = $this->getValidData();
        $dadosProfSemEscola['id_escola'] = null;
        $validatorProfSemEscola = Validator::make($dadosProfSemEscola, $request->rules());
        $this->assertTrue($validatorProfSemEscola->fails());
        $this->assertArrayHasKey('id_escola', $validatorProfSemEscola->errors()->toArray());

        $dadosAdminComEscola = $this->getValidData();
        $dadosAdminComEscola['tipo_usuario'] = 'administrador';
        $validatorAdminComEscola = Validator::make($dadosAdminComEscola, $request->rules());
        $this->assertTrue($validatorAdminComEscola->fails());
        $this->assertArrayHasKey('id_escola', $validatorAdminComEscola->errors()->toArray());
    }

    /**
     * @test
     */
    public function nao_pode_criar_terceiro_diretor_ativo_na_escola()
    {
        Usuario::factory(2)->create(['tipo_usuario' => 'diretor', 'status_aprovacao' => 'ativo', 'id_escola' => $this->escola->id_escola]);
        $request = new StoreUserRequest();
        $dados = $this->getValidData();
        $dados['tipo_usuario'] = 'diretor';
        $dados['status_aprovacao'] = 'ativo';

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_escola', $validator->errors()->toArray());
         $this->assertStringContainsString('limite de 2 (dois) diretores ativos', $validator->errors()->first('id_escola'));
    }

    private function getValidData(): array
    {
        Validator::extend('celular_com_ddd', fn() => true);
        Validator::extend('cpf', fn() => true); 
        Validator::extend('rg_valido', fn() => true); 

        return [
            'nome_completo' => 'Novo Usuario Teste',
            'username' => 'novo.usuario.teste',
            'email' => 'novo@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'data_nascimento' => '1990-01-01',
            'cpf' => '12345678900', 
            'rg' => '1234567', 
            'telefone' => '(11) 98888-7777',
            'formacao' => 'Licenciatura Exemplo',
            'tipo_usuario' => 'professor',
            'status_aprovacao' => 'ativo',
            'id_escola' => $this->escola->id_escola,
        ];
    }

    public static function validationProviderObrigatorios(): array
    {
        return [
            ['nome_completo', ''],
            ['username', ''],
            ['email', ''],
            ['password', ''],
            ['data_nascimento', ''],
            ['cpf', ''],
            ['rg', ''],
            ['telefone', ''],
            ['formacao', ''],
            ['tipo_usuario', ''],
            ['status_aprovacao', ''],
        ];
    }
}