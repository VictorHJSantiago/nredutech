<?php

namespace Tests\Unit\Notifications;

use Tests\TestCase;
use App\Mail\NotificationMail;
use Illuminate\Mail\Mailable;

class NotificationMailTest extends TestCase
{
    public function test_email_de_notificacao_constroi_corretamente()
    {
        $subject = 'Assunto do Email';
        $message = 'Esta é uma mensagem de teste.';
        
        $mailable = new NotificationMail($subject, $message);
        $content = $mailable->content();

        $this->assertInstanceOf(Mailable::class, $mailable);
        $this->assertEquals($subject, $mailable->envelope()->subject);
        $this->assertEquals('emails.notification', $content->view);
        $this->assertArrayHasKey('message', $content->with);
        $this->assertEquals($message, $content->with['message']);
    }
}