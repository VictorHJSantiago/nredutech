<?php

namespace Tests\Feature\Auth;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_users_can_authenticate_with_active_status_using_email()
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
    
    public function test_users_can_authenticate_with_active_status_using_username()
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

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('index'));
    }

    public function test_users_cannot_authenticate_with_pending_status()
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

    public function test_users_cannot_authenticate_with_bloqueado_status()
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

    public function test_users_cannot_authenticate_with_invalid_password()
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

    public function test_users_can_logout()
    {
        $user = Usuario::factory()->create(['status_aprovacao' => 'ativo']);
        $this->actingAs($user);

        $this->assertAuthenticatedAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}