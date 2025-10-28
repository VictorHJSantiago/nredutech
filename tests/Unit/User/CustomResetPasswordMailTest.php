<?php

namespace Tests\Unit\User;
use Tests\TestCase;
use App\Mail\CustomResetPasswordMail;
use App\Models\Usuario; 
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomResetPasswordMailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mailable_de_reset_de_senha_e_construido_corretamente()
    {
        $usuario = Usuario::factory()->make(['nome_completo' => 'Usuario Teste', 'email' => 'teste@example.com']);
        $token = 'meu_token_secreto_123';
        $resetUrl = URL::temporarySignedRoute(
            'password.reset', now()->addMinutes(60), ['token' => $token, 'email' => $usuario->email]
        );
        $mailable = new CustomResetPasswordMail($usuario, $token);
        $mailable->assertHasSubject('Redefinição de Senha - NREduTech');

        $mailable->assertSeeInHtml("Olá {$usuario->nome_completo},");
        $mailable->assertSeeInHtml($resetUrl); 
        $mailable->assertSeeInHtml('Redefinir Senha'); 
    }
}