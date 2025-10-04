<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Usuario;
use App\Models\Agendamento;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
    ];

    public function boot(): void
    {
        Gate::define('cancelar-agendamento', function (Usuario $user, Agendamento $agendamento) {
            if (strtolower(trim($user->tipo_usuario)) === 'administrador') {
                return true;
            }
            if (!$agendamento->oferta || !$agendamento->oferta->turma || !$agendamento->oferta->professor) {
                return false;
            }

            if ($user->id_usuario === $agendamento->oferta->id_professor) {
                return true;
            }
            if (strtolower(trim($user->tipo_usuario)) === 'diretor' && $user->id_escola === $agendamento->oferta->turma->id_escola) {
                return true;
            }
            return false;
        });
    }
}