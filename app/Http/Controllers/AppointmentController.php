<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Agendamento;
use App\Models\OfertaComponente;
use App\Models\RecursoDidatico;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMail;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $authUser = Auth::user();
        $ofertasQuery = OfertaComponente::with(['componenteCurricular', 'turma', 'professor']);

        if ($authUser->tipo_usuario === 'diretor' && $authUser->id_escola) {
            $ofertasQuery->whereHas('turma', fn($q) => $q->where('id_escola', $authUser->id_escola));
        } elseif ($authUser->tipo_usuario === 'professor') {
            $ofertasQuery->where('id_professor', $authUser->id_usuario);
        }
        $ofertas = $ofertasQuery->get();

        $sortBy = $request->query('sort_by', 'data_hora_inicio');
        $order = $request->query('order', 'asc');
        $allowedSorts = ['recurso_nome', 'data_hora_inicio', 'turma_serie'];

        $meusAgendamentosQuery = Agendamento::with(['recurso', 'oferta.turma'])
            ->whereHas('oferta', fn ($query) => $query->where('id_professor', $authUser->id_usuario))
            ->where('data_hora_inicio', '>=', now());

        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'recurso_nome') {
                $meusAgendamentosQuery->join('recursos_didaticos', 'agendamentos.id_recurso', '=', 'recursos_didaticos.id_recurso')
                    ->orderBy('recursos_didaticos.nome', $order);
            } elseif ($sortBy === 'turma_serie') {
                $meusAgendamentosQuery->join('oferta_componentes', 'agendamentos.id_oferta', '=', 'oferta_componentes.id_oferta')
                    ->join('turmas', 'oferta_componentes.id_turma', '=', 'turmas.id_turma')
                    ->orderBy('turmas.serie', $order);
            } else {
                $meusAgendamentosQuery->orderBy($sortBy, $order);
            }
        }

        $meusAgendamentos = $meusAgendamentosQuery->select('agendamentos.*')->paginate(5, ['*'], 'meus_agendamentos_page');

        return view('appointments.index', [
            'ofertas' => $ofertas,
            'meusAgendamentos' => $meusAgendamentos,
            'now' => now()->toIso8601String(),
            'sortBy' => $sortBy,
            'order' => $order
        ]);
    }

    public function getCalendarEvents(Request $request): AnonymousResourceCollection
    {
        $authUser = Auth::user();
        $query = Agendamento::query()
            ->with(['recurso', 'oferta.turma.escola', 'oferta.professor', 'oferta.componenteCurricular'])
            ->whereBetween('data_hora_inicio', [$request->start, $request->end]);

        if ($authUser->tipo_usuario !== 'administrador' && $authUser->id_escola) {
            $query->whereHas('oferta.turma', function ($q) use ($authUser) {
                $q->where('id_escola', $authUser->id_escola);
            });
        }
        $agendamentos = $query->get();
        return AppointmentResource::collection($agendamentos);
    }

    public function getAvailabilityForDate(Request $request): JsonResponse
    {
        $request->validate(['date' => 'required|date']);
        $date = Carbon::parse($request->date)->setTimezone(config('app.timezone'));
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();
        $authUser = Auth::user();

        $recursosQuery = RecursoDidatico::query();
        $agendamentosQuery = Agendamento::query();

        if ($authUser->tipo_usuario !== 'administrador' && $authUser->id_escola) {
            $escolaId = $authUser->id_escola;
            $agendamentosQuery->whereHas('oferta.turma', fn($q) => $q->where('id_escola', $escolaId));
        }

        $agendamentosDoDia = $agendamentosQuery
            ->whereBetween('data_hora_inicio', [$startOfDay, $endOfDay])
            ->get();
            
        $recursosAgendadosIds = $agendamentosDoDia->pluck('id_recurso')->unique()->toArray();
        
        $recursosDisponiveis = $recursosQuery->where('status', 'funcionando')
             ->whereNotIn('id_recurso', $recursosAgendadosIds)
             ->paginate(5, ['id_recurso', 'nome', 'quantidade'], 'disponiveis_page');

        $agendadosPaginados = Agendamento::with(['recurso', 'oferta.turma', 'oferta.professor', 'oferta.componenteCurricular'])
            ->whereIn('id_agendamento', $agendamentosDoDia->pluck('id_agendamento'))
            ->orderBy('data_hora_inicio')
            ->paginate(5, ['*'], 'agendados_page');

        return response()->json([
            'disponiveis' => $recursosDisponiveis,
            'agendados' => $agendadosPaginados
        ]);
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $inicio = Carbon::parse($validatedData['data_hora_inicio']);

        if ($inicio->hour >= 23 || $inicio->hour < 6) {
            return response()->json(['message' => 'Não é permitido criar agendamentos entre 23:00 e 06:00.'], 422);
        }
        
        $validatedData['status'] = 'agendado';

        $agendamento = Agendamento::create($validatedData);
        
        if ($agendamento->oferta && $professor = $agendamento->oferta->professor) {
            $titulo = 'Novo Agendamento Criado';
            $mensagem = "Um novo agendamento para o recurso '{$agendamento->recurso->nome}' foi criado para o professor(a) {$professor->nome_completo}.";
            
            Notificacao::create(['titulo' => $titulo, 'mensagem' => $mensagem, 'data_envio' => now(), 'status_mensagem' => 'enviada', 'id_usuario' => $professor->id_usuario]);
            if ($professor->preferencias && $professor->preferencias->notif_email) {
                Mail::to($professor->email)->send(new NotificationMail($titulo, $mensagem));
            }
        }
        
        return response()->json(['message' => 'Agendamento criado com sucesso!'], 201);
    }
}