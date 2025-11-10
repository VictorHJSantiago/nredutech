<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use App\Mail\CustomResetPasswordMail;
use App\Models\Usuario;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomResetPasswordMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_de_redefinicao_de_senha_constroi_corretamente()
    {
        $user = Usuario::factory()->make(['nome_completo' => 'Usuário Teste']);
        $token = 'test_token_123456';
        $email = 'test@example.com';
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);

        $mailable = new CustomResetPasswordMail($resetUrl, $user->nome_completo);

        $mailable->to($email);

        $mailable->assertHasSubject('NREduTech - Redefinição de Senha');
        $mailable->assertTo($email);
        $mailable->assertSeeInHtml($resetUrl);
        $mailable->assertSeeInHtml($user->nome_completo);
        $mailable->assertSeeInHtml('Você está recebendo este e-mail');
    }
}