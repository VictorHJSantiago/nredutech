<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;

class RegisterUserRequestTest extends TestCase
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

    private function getValidData(): array
    {
        return [
            'name' => 'Usuário Teste Válido',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Senha@Forte!123_abcXYZ',
            'password_confirmation' => 'Senha@Forte!123_abcXYZ',
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escola->id_escola,
            'data_nascimento' => '2000-01-01',
            'cpf' => '11144477735',
            'rg' => '123456789',
            'telefone' => '(42) 99999-8888',
            'rco_siape' => '1234567',
            'formacao' => 'Formação de Teste',
            'area_formacao' => 'Área de Teste',
        ];
    }

    private function getPatchedRules(RegisterUserRequest $request): array
    {
        $rules = $request->rules();
        $fieldsToPatch = ['username', 'email', 'cpf', 'rg', 'rco_siape'];

        foreach ($fieldsToPatch as $field) {
            if (isset($rules[$field])) {
                $ruleSet = is_array($rules[$field]) ? $rules[$field] : explode('|', $rules[$field]);
                
                $newRuleSet = array_map(function ($rule) {
                    if (is_string($rule) && str_contains($rule, 'unique:users')) {
                        return str_replace('unique:users', 'unique:usuarios', $rule);
                    }
                    return $rule;
                }, $ruleSet);

                $rules[$field] = is_array($rules[$field]) ? $newRuleSet : implode('|', $newRuleSet);
            }
        }
        return $rules;
    }

    public function test_authorize_retorna_true()
    {
        $request = new RegisterUserRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_validacao_passa_com_dados_validos()
    {
        $request = new RegisterUserRequest();
        $data = $this->getValidData();
        $rules = $this->getPatchedRules($request);
        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    #[DataProvider('missingFieldsProvider')]
    public function test_validacao_falha_em_campos_obrigatorios(string $field)
    {
        $request = new RegisterUserRequest();
        $data = $this->getValidData();
        unset($data[$field]);
        $rules = $this->getPatchedRules($request);
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($field, $validator->errors()->toArray());
    }

    #[DataProvider('uniqueFieldsProvider')]
    public function test_validacao_falha_em_campos_unicos(string $field)
    {
        Usuario::factory()->create([$field => 'valor_duplicado']);
        
        $request = new RegisterUserRequest();
        $data = $this->getValidData();
        $data[$field] = 'valor_duplicado';
        $rules = $this->getPatchedRules($request);
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($field, $validator->errors()->toArray());
    }

    public function test_validacao_falha_em_tipo_usuario_invalido()
    {
        $request = new RegisterUserRequest();
        $data = $this->getValidData();
        $data['tipo_usuario'] = 'administrador';
        $rules = $this->getPatchedRules($request);
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tipo_usuario', $validator->errors()->toArray());
    }

    public function test_validacao_falha_na_confirmacao_de_senha()
    {
        $request = new RegisterUserRequest();
        $data = $this->getValidData();
        $data['password_confirmation'] = 'senha_errada';
        $rules = $this->getPatchedRules($request);
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public static function missingFieldsProvider(): array
    {
        return [
            ['name'],
            ['username'],
            ['email'],
            ['password'],
            ['tipo_usuario'],
            ['data_nascimento'],
            ['cpf'],
            ['rg'],
            ['telefone'],
            ['rco_siape'],
            ['formacao'],
            ['area_formacao'],
        ];
    }

    public static function uniqueFieldsProvider(): array
    {
        return [
            ['username'],
            ['email'],
            ['cpf'],
            ['rg'],
        ];
    }
}