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
        $stats = [
            'total_usuarios' => Usuario::count(),
            'total_escolas' => Escola::count(),
            'recursos_disponiveis' => RecursoDidatico::where('status', 'funcionando')->count(),
            'agendamentos_hoje' => Agendamento::whereDate('data_hora_inicio', today())->count(),
        ];

        $proximosAgendamentos = Agendamento::with(['recurso', 'oferta.professor', 'oferta.turma'])
            ->where('data_hora_inicio', '>=', now())
            ->orderBy('data_hora_inicio', 'asc')
            ->take(5)
            ->get();

        $ultimosUsuarios = Usuario::with('escola')
            ->latest() 
            ->take(5)
            ->get();
            
        return view('index', [
            'stats' => $stats,
            'proximosAgendamentos' => $proximosAgendamentos,
            'ultimosUsuarios' => $ultimosUsuarios,
            'usuarioLogado' => Auth::user(), 
        ]);
    }
}