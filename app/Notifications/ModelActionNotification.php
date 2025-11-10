<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use App\Models\Usuario;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ModelActionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $action;
    public Model $model;
    public Usuario $actor;
    public string $modelType;
    public string $modelName;

    public function __construct(string $action, Model $model, Usuario $actor)
    {
        $this->action = $action;
        $this->model = $model;
        $this->actor = $actor;
        $this->modelType = class_basename($model);
        $this->modelName = $model->nome_completo ?? $model->nome ?? $model->id_escola ?? $model->id_turma ?? 'N/A';
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'action' => $this->action,
            'actor_id' => $this->actor->id_usuario,
            'actor_name' => $this->actor->nome_completo,
            'model_type' => $this->modelType,
            'model_name' => $this->modelName,
            'model_id' => $this->model->getKey(),
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
        ];
    }
}