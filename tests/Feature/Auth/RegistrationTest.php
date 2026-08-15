<?php

namespace Tests\Feature\Auth;

use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function createEscola()
    {
        $municipio = Municipio::create(['nome' => 'Curitiba']);
        
        return Escola::create([
            'nome' => 'Escola Teste',
            'nivel_ensino' => 'Médio',
            'tipo' => 'Estadual',
            'id_municipio' => $municipio->id_municipio
        ]);
    }

    public function test_tela_de_cadastro_pode_ser_renderizada()
    {
        $this->createEscola();
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
        $response->assertViewHas('escolas');
    }

    public function test_novos_usuarios_podem_se_cadastrar()
    {
        $escola = $this->createEscola();

        $data = [
            'name' => 'Usuário de Teste',
            'username' => 'usuarioteste',
            'email' => 'teste@exemplo.com',
            'tipo_usuario' => 'professor',
            'id_escola' => $escola->id_escola,
            'data_nascimento' => '1990-01-01',
            'cpf' => '905.979.410-92',
            'rg' => '12.345.678-9',
            'telefone' => '(42) 99999-9999',
            'rco_siape' => 'RCO123456',
            'formacao' => 'Licenciatura em Testes',
            'area_formacao' => 'TI',
            'password' => 'PasswordValido!123',
            'password_confirmation' => 'PasswordValido!123',
        ];

        $response = $this->post('/register', $data);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Cadastro realizado com sucesso! Aguarde a aprovação do administrador ou diretor.');
        
        $this->assertDatabaseHas('usuarios', [
            'username' => 'usuarioteste',
            'email' => 'teste@exemplo.com',
            'status_aprovacao' => 'pendente',
            'tipo_usuario' => 'professor',
        ]);
    }

    public function test_cadastro_falha_com_dados_invalidos()
    {
        $response = $this->post('/register', [
            'name' => 'Teste',
            'email' => 'nao-e-um-email',
            'password' => 'curto',
            'password_confirmation' => 'diferente',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_cadastro_falha_se_tipo_admin_for_selecionado()
    {
        $escola = $this->createEscola();
        
        $data = [
            'name' => 'Admin Fake',
            'username' => 'adminfake',
            'email' => 'admin@fake.com',
            'tipo_usuario' => 'administrador',
            'id_escola' => $escola->id_escola,
            'data_nascimento' => '1990-01-01',
            'cpf' => '905.979.410-92',
            'rg' => '99.999.999-9',
            'telefone' => '(42) 99999-9999',
            'rco_siape' => '123456',
            'formacao' => 'N/A',
            'area_formacao' => 'N/A',
            'password' => 'PasswordValido!123',
            'password_confirmation' => 'PasswordValido!123',
        ];

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors('tipo_usuario');
        $this->assertDatabaseMissing('usuarios', ['email' => $data['email']]);
    }
}