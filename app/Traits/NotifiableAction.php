<?php

namespace App\Traits;

use App\Models\Escola;
use App\Models\Usuario;
use App\Notifications\ModelActionNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

trait NotifiableAction
{
    protected function notifyAction(string $action, Model $model, Usuario $actor)
    {
        $recipients = $this->getRecipients($model, $actor);
        
        Notification::send($recipients, new ModelActionNotification($action, $model, $actor));
    }

    private function getRecipients(Model $model, Usuario $actor)
    {
        $admins = Usuario::where('tipo_usuario', 'administrador')->get();
        $actorCollection = collect([$actor]);
        $directors = collect();

        $school = null;
        if ($model instanceof Escola) {
            $school = $model;
        } elseif (method_exists($model, 'escola') && $model->escola) {
            $school = $model->escola;
        }

        if ($school) {
            $directors = $school->usuarios()->where('tipo_usuario', 'diretor')->get();
        }

        return $admins->merge($actorCollection)->merge($directors)->unique('id_usuario');
    }
}