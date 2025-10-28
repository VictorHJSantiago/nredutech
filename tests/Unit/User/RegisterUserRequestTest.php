<?php

namespace Tests\Unit\User;
use Tests\TestCase;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola;
use App\Models\Municipio;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterUserRequestTest extends TestCase
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
    public function campos_obrigatorios_falham_se_ausentes($campo, $valorAusente)
    {
        $request = new RegisterUserRequest();
        $dados = $this->getValidData();
        $dados[$campo] = $valorAusente;

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($campo, $validator->errors()->toArray());
    }

    /** @test */
    public function validacao_passa_com_dados_validos()
    {
        $request = new RegisterUserRequest();
        $dados = $this->getValidData();
        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails());
    }

     /** @test */
    public function tipo_usuario_so_pode_ser_professor_ou_diretor()
    {
        $request = new RegisterUserRequest();
        $dados = $this->getValidData();
        $dados['tipo_usuario'] = 'administrador'; 
        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tipo_usuario', $validator->errors()->toArray());
    }

    private function getValidData(): array
    {
        Validator::extend('celular_com_ddd', fn() => true); 
        Validator::extend('cpf', fn() => true); 
        Validator::extend('rg_valido', fn() => true); 
        return [
            'nome_completo' => 'Auto Cadastro Teste',
            'username' => 'autocadastro.teste',
            'email' => 'autocadastro@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'data_nascimento' => '1995-05-10',
            'cpf' => '98765432100', 
            'rg' => '7654321', 
            'telefone' => '(21) 97777-6666', 
            'formacao' => 'Outra Licenciatura',
            'tipo_usuario' => 'professor', 
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
            ['id_escola', null], 
        ];
    }
}