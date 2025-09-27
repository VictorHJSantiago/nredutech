<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Agendamento;
use App\Models\OfertaComponente;
use App\Models\RecursoDidatico;
use App\Models\Usuario; 
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth; 
use Illuminate\View\View;
use App\Models\Notificacao;

class AppointmentController extends Controller
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

            return AppointmentResource::collection($agendamentos);
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

    public function store(StoreAppointmentRequest $request): AppointmentResource
    {
        $agendamento = Agendamento::create($request->validated());
        $agendamento->load(['recurso', 'oferta.professor']);

        if ($agendamento->oferta && $agendamento->oferta->professor) {
            Notificacao::create([
                'titulo' => 'Novo Agendamento Criado',
                'mensagem' => "Um novo agendamento para o recurso '{$agendamento->recurso->nome}' foi criado.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $agendamento->oferta->id_professor,
            ]);
        }
        return new AppointmentResource($agendamento);
    }

    public function show(Agendamento $agendamento): AppointmentResource
    {
        $agendamento->load(['recurso', 'oferta.turma', 'oferta.professor']);
        return new AppointmentResource($agendamento);
    }

    public function update(UpdateAppointmentRequest $request, Agendamento $agendamento): AppointmentResource
    {
        $agendamento->update($request->validated());
        $agendamento->load(['recurso', 'oferta.professor']);

        if ($agendamento->oferta && $agendamento->oferta->professor) {
            Notificacao::create([
                'titulo' => 'Agendamento Atualizado',
                'mensagem' => "O agendamento para o recurso '{$agendamento->recurso->nome}' foi atualizado.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $agendamento->oferta->id_professor,
            ]);
        }
        return new AppointmentResource($agendamento->fresh());
    }

    public function destroy(Agendamento $agendamento): JsonResponse
    {
        $agendamento->load(['recurso', 'oferta.professor']);
        $professorId = $agendamento->oferta->id_professor;

        if ($agendamento->oferta && $agendamento->oferta->professor) {
            Notificacao::create([
                'titulo' => 'Agendamento Cancelado',
                'mensagem' => "O agendamento para o recurso '{$agendamento->recurso->nome}' foi cancelado.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $professorId,
            ]);
        }

        $agendamento->delete();
        return response()->json(null, 204);
    }
}