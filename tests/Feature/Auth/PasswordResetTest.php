<?php

namespace Tests\Feature\Auth;

use App\Models\Usuario;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered()
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested()
    {
        $user = Usuario::factory()->create();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    public function test_reset_password_screen_can_be_rendered()
    {
        $user = Usuario::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->get('/reset-password/' . $token . '?email=' . $user->email);

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
    }

    public function test_password_can_be_reset_with_valid_token()
    {
        Event::fake();

        $user = Usuario::factory()->create();
        $token = Password::broker()->createToken($user);
        $newPassword = 'nova-senha-123';

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', __('passwords.reset'));
        
        $this->assertTrue(Hash::check($newPassword, $user->refresh()->password));
        Event::assertDispatched(PasswordReset::class);
    }

    public function test_password_reset_fails_with_invalid_token()
    {
        $user = Usuario::factory()->create();
        $newPassword = 'nova-senha-123';

        $response = $this->post('/reset-password', [
            'token' => 'token-invalido',
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertSessionHasErrors('email');
    }
}