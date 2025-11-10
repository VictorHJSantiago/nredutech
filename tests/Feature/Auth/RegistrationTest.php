<?php

namespace Tests\Feature\Auth;

use App\Models\Escola;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        Escola::factory()->create();
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
        $response->assertViewHas('escolas');
    }

    public function test_new_users_can_register()
    {
        $escola = Escola::factory()->create();

        $data = [
            'nome_completo' => 'Usuário de Teste',
            'username' => 'usuarioteste',
            'email' => 'teste@exemplo.com',
            'tipo_usuario' => 'professor',
            'id_escola' => $escola->id_escola,
            'data_nascimento' => '1990-01-01',
            'cpf' => '123.456.789-00',
            'rg' => '12.345.678-9',
            'telefone' => '(42) 99999-9999',
            'rco_siape' => 'RCO123456',
            'formacao' => 'Licenciatura em Testes',
            'area_formacao' => 'TI',
            'password' => 'passwordValido123',
            'password_confirmation' => 'passwordValido123',
        ];

        $response = $this->post('/register', $data);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Cadastro realizado com sucesso! Aguarde a aprovação de um administrador.');
        
        $this->assertDatabaseHas('usuarios', [
            'username' => 'usuarioteste',
            'email' => 'teste@exemplo.com',
            'status_aprovacao' => 'pendente',
            'tipo_usuario' => 'professor',
        ]);
    }

    public function test_registration_fails_with_invalid_data()
    {
        $response = $this->post('/register', [
            'nome_completo' => 'Teste',
            'email' => 'nao-e-um-email',
            'password' => 'curto',
            'password_confirmation' => 'diferente',
        ]);

        $response->assertSessionHasErrors(['nome_completo', 'email', 'password']);
    }

    public function test_registration_fails_if_admin_type_is_selected()
    {
        $escola = Escola::factory()->create();
        $data = Usuario::factory()->make([
            'tipo_usuario' => 'administrador',
            'id_escola' => $escola->id_escola
        ])->toArray();
        $data['password'] = 'passwordValido123';
        $data['password_confirmation'] = 'passwordValido123';

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors('tipo_usuario');
        $this->assertDatabaseMissing('usuarios', ['email' => $data['email']]);
    }
}