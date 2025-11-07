<?php

namespace App\Notifications;

use App\Models\Escola;
use App\Models\Municipio;
use App\Models\RecursoDidatico;
use App\Models\Turma;
use App\Models\Usuario;
use App\Models\ComponenteCurricular;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ModelActionNotification extends Notification
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
        $this->modelType = $this->getModelType($model);
        $this->modelName = $this->getModelName($model);
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $message = sprintf(
            '%s %s %s: %s',
            $this->actor->nome_completo,
            $this->action,
            $this->modelType,
            $this->modelName
        );

        return [
            'message' => $message,
            'actor_id' => $this->actor->id_usuario,
            'actor_name' => $this->actor->nome_completo,
            'action' => $this->action,
            'model_type' => $this->modelType,
            'model_name' => $this->modelName,
            'model_id' => $this->model->getKey()
        ];
    }

    private function getModelType(Model $model): string
    {
        return match (get_class($model)) {
            Municipio::class => 'o Município',
            Escola::class => 'a Escola',
            Turma::class => 'a Turma',
            ComponenteCurricular::class => 'a Disciplina',
            RecursoDidatico::class => 'o Recurso Didático',
            Usuario::class => 'o Usuário',
            default => 'um item'
        };
    }

    private function getModelName(Model $model): string
    {
        if ($model instanceof Usuario) {
            return $model->nome_completo;
        }
        if ($model instanceof Turma) {
            return $model->serie;
        }
        return $model->nome ?? 'ID ' . $model->getKey();
    }
}