<?php

namespace Tests\Feature\Auth;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_tela_de_login_pode_ser_renderizada()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_usuarios_podem_se_autenticar_com_status_ativo_usando_email()
    {
        $user = Usuario::factory()->create([
            'email' => 'teste@exemplo.com',
            'password' => Hash::make('senha123'),
            'status_aprovacao' => 'ativo',
        ]);

        $response = $this->post('/login', [
            'email' => 'teste@exemplo.com',
            'password' => 'senha123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('index'));
    }

    public function test_usuarios_nao_podem_se_autenticar_usando_username_pois_exige_email()
    {
        $user = Usuario::factory()->create([
            'username' => 'usuarioativo',
            'password' => Hash::make('senha123'),
            'status_aprovacao' => 'ativo',
        ]);

        $response = $this->post('/login', [
            'email' => 'usuarioativo',
            'password' => 'senha123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_usuarios_nao_podem_se_autenticar_com_status_pendente()
    {
        Usuario::factory()->create([
            'email' => 'pendente@exemplo.com',
            'password' => Hash::make('senha123'),
            'status_aprovacao' => 'pendente',
        ]);

        $response = $this->post('/login', [
            'email' => 'pendente@exemplo.com',
            'password' => 'senha123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_usuarios_nao_podem_se_autenticar_com_status_bloqueado()
    {
        Usuario::factory()->create([
            'email' => 'bloqueado@exemplo.com',
            'password' => Hash::make('senha123'),
            'status_aprovacao' => 'bloqueado',
        ]);

        $response = $this->post('/login', [
            'email' => 'bloqueado@exemplo.com',
            'password' => 'senha123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_usuarios_nao_podem_se_autenticar_com_senha_invalida()
    {
        Usuario::factory()->create([
            'email' => 'teste@exemplo.com',
            'password' => Hash::make('senha123'),
            'status_aprovacao' => 'ativo',
        ]);

        $response = $this->post('/login', [
            'email' => 'teste@exemplo.com',
            'password' => 'senha-errada',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_usuarios_podem_fazer_logout()
    {
        $user = Usuario::factory()->create(['status_aprovacao' => 'ativo']);
        $this->actingAs($user);

        $this->assertAuthenticatedAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}