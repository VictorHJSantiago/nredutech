<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgendamentoRequest;
use App\Http\Requests\UpdateAgendamentoRequest;
use App\Http\Resources\AgendamentoResource;
use App\Models\Agendamento;
use App\Models\OfertaComponente;
use App\Models\RecursoDidatico;
use App\Models\Usuario; 
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth; 
use Illuminate\View\View;

class AgendamentoController extends Controller
{
    /**
     *
     * @param Request $request
     * @return View|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $authUser = Auth::user();
        $domainUser = $authUser ? Usuario::where('email', $authUser->email)->first() : null;
        $escolaId = null;

        if ($domainUser && $domainUser->tipo_usuario !== 'administrador' && $domainUser->id_escola) {
            $escolaId = $domainUser->id_escola;
        }

        if ($request->wantsJson()) {
            $request->validate([
                'start' => 'required|date',
                'end' => 'required|date|after_or_equal:start',
            ]);

            $query = Agendamento::query()
                ->with(['recurso', 'oferta.turma', 'oferta.professor', 'oferta.componenteCurricular'])
                ->whereHas('recurso') 
                ->whereHas('oferta.turma'); 

            if ($escolaId) {
                $query->whereHas('oferta.turma', function ($q) use ($escolaId) {
                    $q->where('id_escola', $escolaId);
                });
            }

            $query->where('data_hora_inicio', '<', $request->end)
                  ->where('data_hora_fim', '>', $request->start);

            $agendamentos = $query->get();

            return AgendamentoResource::collection($agendamentos);
        }

        $recursos = RecursoDidatico::where('status', 'funcionando')->get();
        $ofertasQuery = OfertaComponente::with(['componenteCurricular', 'turma', 'professor'])
                        ->whereHas('turma')
                        ->whereHas('componenteCurricular');

        if ($escolaId) {
            $ofertasQuery->whereHas('turma', function ($q) use ($escolaId) {
                $q->where('id_escola', $escolaId);
            });
        }

        $ofertas = $ofertasQuery->get();
        $reservadosQuery = Agendamento::query()
            ->with(['recurso', 'oferta.professor', 'oferta.turma'])
            ->where('status', 'agendado') 
            ->where('data_hora_inicio', '>=', now())
            ->orderBy('data_hora_inicio', 'asc');

        $disponiveisQuery = Agendamento::query()
            ->with(['recurso', 'oferta.professor'])
            ->where('status', 'livre') 
            ->where('data_hora_inicio', '>=', now())
            ->orderBy('data_hora_inicio', 'asc');

        if ($escolaId) {
            $reservadosQuery->whereHas('oferta.turma', function ($q) use ($escolaId) {
                $q->where('id_escola', $escolaId);
            });
            $disponiveisQuery->whereHas('oferta.turma', function ($q) use ($escolaId) {
                $q->where('id_escola', $escolaId);
            });
        }

        $reservados = $reservadosQuery->paginate(5, ['*'], 'reservados_page');
        $disponiveis = $disponiveisQuery->paginate(5, ['*'], 'disponiveis_page');

        return view('appointments.index', [
            'recursos' => $recursos,
            'ofertas' => $ofertas,
            'now' => now()->toIso8601String(),
            'reservados' => $reservados,     
            'disponiveis' => $disponiveis, 
        ]);
    }
    public function store(StoreAgendamentoRequest $request): AgendamentoResource
    {
        $agendamento = Agendamento::create($request->validated());
        return new AgendamentoResource($agendamento->load(['recurso', 'oferta']));
    }

    public function show(Agendamento $agendamento): AgendamentoResource
    {
        $agendamento->load(['recurso', 'oferta.turma', 'oferta.professor']);
        return new AgendamentoResource($agendamento);
    }

    public function update(UpdateAgendamentoRequest $request, Agendamento $agendamento): AgendamentoResource
    {
        $agendamento->update($request->validated());
        return new AgendamentoResource($agendamento->fresh()->load(['recurso', 'oferta']));
    }

    public function destroy(Agendamento $agendamento): JsonResponse
    {
        
        $agendamento->load('oferta');
        $professorQueReservouId = $agendamento->oferta->id_professor;

        $authUser = Auth::user(); 
        $usuarioAtual = Usuario::where('email', $authUser->email)->first(); 

        $podeExcluir = false;
        if ($usuarioAtual) {
            if (in_array($usuarioAtual->tipo_usuario, ['administrador', 'diretor'])) {
                $podeExcluir = true;
            } 
            elseif ($usuarioAtual->id_usuario == $professorQueReservouId) {
                $podeExcluir = true;
            }
        }

        if (!$podeExcluir) {
            return response()->json(['message' => 'Você não tem permissão para excluir este agendamento.'], 403);
        }

        if ($agendamento->data_hora_inicio < now()->addMinutes(10)) {
            return response()->json(['message' => 'Não é possível excluir agendamentos passados ou faltando menos de 10 minutos.'], 403);
        }

        $agendamento->delete();
        return response()->json(null, 204);
    }
}