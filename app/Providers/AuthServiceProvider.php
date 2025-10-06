<?php

namespace App\Providers;

use App\Models\Agendamento;
use App\Models\Usuario;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('cancelar-agendamento', function (Usuario $user, Agendamento $agendamento) {
            
            if ($user->tipo_usuario === 'administrador') {
                return true;
            }

            $agendamento->loadMissing('oferta.turma', 'oferta.professor');

            if (!$agendamento->oferta || !$agendamento->oferta->professor || !$agendamento->oferta->turma) {
                return false;
            }

            if ($user->id_usuario == $agendamento->oferta->id_professor) {
                return true;
            }

            if ($user->tipo_usuario === 'diretor' && $user->id_escola == $agendamento->oferta->turma->id_escola) {
                return true;
            }

            return false;
        });
    }
}