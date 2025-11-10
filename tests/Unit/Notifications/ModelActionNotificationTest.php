<?php

namespace Tests\Unit\Notifications;

use Tests\TestCase;
use App\Notifications\ModelActionNotification;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ModelActionNotificationTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $actor;
    private Usuario $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actor = Usuario::factory()->create();
        $this->model = Usuario::factory()->create();
    }

    public function test_notificacao_retorna_canais_via_corretos()
    {
        $notification = new ModelActionNotification('created', $this->model, $this->actor);
        
        $this->assertEquals(['database'], $notification->via(new Usuario()));
    }

    public function test_notificacao_retorna_array_de_banco_de_dados_correto()
    {
        $notification = new ModelActionNotification('updated', $this->model, $this->actor);
        $databaseMessage = $notification->toDatabase(new Usuario());

        $this->assertInstanceOf(DatabaseMessage::class, $databaseMessage);
        
        $data = $databaseMessage->data;
        $this->assertEquals('updated', $data['action']);
        $this->assertEquals($this->actor->id_usuario, $data['actor_id']);
        $this->assertEquals($this->actor->nome_completo, $data['actor_name']);
        $this->assertEquals(class_basename($this->model), $data['model_type']);
        $this->assertEquals($this->model->nome_completo, $data['model_name']);
        $this->assertEquals($this->model->id_usuario, $data['model_id']);
    }
}