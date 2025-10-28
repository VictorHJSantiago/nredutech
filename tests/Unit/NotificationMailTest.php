<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Mail\NotificationMail;

class NotificationMailTest extends TestCase
{
    /** @test */
    public function mailable_de_notificacao_generica_e_construido_corretamente()
    {
        $titulo = 'Atualização Importante';
        $mensagem = 'O status do seu pedido foi atualizado.';
        $mailable = new NotificationMail($titulo, $mensagem);
        $mailable->assertHasSubject('NREduTech - Atualização Importante');
        $mailable->assertSeeInHtml($titulo);
        $mailable->assertSeeInHtml($mensagem); 
    }
}