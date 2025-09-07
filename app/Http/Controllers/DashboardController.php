<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Usuario;
use App\Models\Escola;
use App\Models\RecursoDidatico;
use App\Models\Agendamento;

class DashboardController extends Controller
{
    public function index(): View
    {
        $usuarioLogado = Auth::user(); 
        $userType = $usuarioLogado->tipo_usuario;
        $userSchoolId = $usuarioLogado->id_escola;

        $stats = [];
        $proximosAgendamentosQuery = Agendamento::query();
        $ultimosUsuariosQuery = Usuario::query();

        if ($userType === 'administrador') {
            $stats = [
                'total_usuarios' => Usuario::count(),
                'total_escolas' => Escola::count(),
                'recursos_disponiveis' => RecursoDidatico::where('status', 'funcionando')->count(),
                'agendamentos_hoje' => Agendamento::whereDate('data_hora_inicio', today())->count(),
            ];
             $ultimosUsuariosQuery = Usuario::with('escola');
             $proximosAgendamentosQuery = Agendamento::with(['recurso', 'oferta.professor', 'oferta.turma']);

        } else {
            if ($userSchoolId) {
                $userIdsDaEscola = Usuario::where('id_escola', $userSchoolId)->pluck('id_usuario');
                $turmaIdsDaEscola = Escola::find($userSchoolId)->turmas()->pluck('id_turma');
                $ofertaIdsDaEscola = \App\Models\OfertaComponente::whereIn('id_turma', $turmaIdsDaEscola)->pluck('id_oferta');

                $stats = [
                    'total_usuarios' => $userIdsDaEscola->count(),
                    'total_escolas' => 1,
                    'recursos_disponiveis' => RecursoDidatico::where('status', 'funcionando')->count(), 
                    'agendamentos_hoje' => Agendamento::whereIn('id_oferta', $ofertaIdsDaEscola)
                                                    ->whereDate('data_hora_inicio', today())
                                                    ->count(),
                ];

                $ultimosUsuariosQuery = Usuario::with('escola')->where('id_escola', $userSchoolId);
                $proximosAgendamentosQuery = Agendamento::with(['recurso', 'oferta.professor', 'oferta.turma'])
                                                     ->whereIn('id_oferta', $ofertaIdsDaEscola);

            } else {
                 $stats = [
                    'total_usuarios' => 0, 'total_escolas' => 0, 'recursos_disponiveis' => RecursoDidatico::where('status', 'funcionando')->count(), 'agendamentos_hoje' => 0,
                ];
            }
        }


        $proximosAgendamentos = $proximosAgendamentosQuery
            ->where('data_hora_inicio', '>=', now())
            ->orderBy('data_hora_inicio', 'asc')
            ->take(5)
            ->get();

        $ultimosUsuarios = $ultimosUsuariosQuery
            ->latest() 
            ->take(5)
            ->get();
            
        return view('index', [
            'stats' => $stats,
            'proximosAgendamentos' => $proximosAgendamentos,
            'ultimosUsuarios' => $ultimosUsuarios,
            'usuarioLogado' => $usuarioLogado, 
        ]);
    }
}