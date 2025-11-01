<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Agendamento;
use App\Models\OfertaComponente;
use App\Models\RecursoDidatico;
use App\Models\Usuario; 
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log; 
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMail;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\AppointmentResource;


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
        $allowedSorts = ['recurso_nome', 'data_hora_inicio', 'turma_serie', 'recurso_quantidade'];

        $meusAgendamentosQuery = Agendamento::with(['recurso', 'oferta.turma'])
            ->whereHas('oferta', fn ($query) => $query->where('id_professor', $authUser->id_usuario))
            ->where('data_hora_inicio', '>=', now());

        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'recurso_nome' || $sortBy === 'recurso_quantidade') {
                $meusAgendamentosQuery->join('recursos_didaticos', 'agendamentos.id_recurso', '=', 'recursos_didaticos.id_recurso');
                $sortField = ($sortBy === 'recurso_nome') ? 'recursos_didaticos.nome' : 'recursos_didaticos.quantidade';
                $meusAgendamentosQuery->orderBy($sortField, $order);
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

        $agendamentosDoDiaQuery = Agendamento::query()->whereBetween('data_hora_inicio', [$startOfDay, $endOfDay]);
        if ($authUser->tipo_usuario !== 'administrador' && $authUser->id_escola) {
            $agendamentosDoDiaQuery->whereHas('oferta.turma', fn($q) => $q->where('id_escola', $authUser->id_escola));
        }
        $recursosAgendadosIds = $agendamentosDoDiaQuery->pluck('id_recurso')->unique();

        $recursosDisponiveisQuery = RecursoDidatico::query()
            ->where('status', 'funcionando')
            ->whereNotIn('id_recurso', $recursosAgendadosIds);
        if ($authUser->tipo_usuario !== 'administrador') {
            $userSchoolId = $authUser->id_escola;
            $recursosDisponiveisQuery->where(function ($q) use ($userSchoolId) {
                if ($userSchoolId) {
                    $q->where('id_escola', $userSchoolId);
                }
                $q->orWhereNull('id_escola'); 
            });
        }

        if ($request->disponiveis_search) {
            $recursosDisponiveisQuery->where('nome', 'like', '%' . $request->disponiveis_search . '%');
        }

        $recursosDisponiveisQuery->orderBy(
            $request->input('disponiveis_sort_by', 'nome'),
            $request->input('disponiveis_order', 'asc')
        );
        $recursosDisponiveis = $recursosDisponiveisQuery->paginate(5, ['*'], 'disponiveis_page');

        $agendadosQuery = Agendamento::with(['recurso', 'oferta.turma', 'oferta.professor', 'oferta.componenteCurricular'])
            ->whereBetween('data_hora_inicio', [$startOfDay, $endOfDay]);

        if ($authUser->tipo_usuario !== 'administrador' && $authUser->id_escola) {
            $agendadosQuery->whereHas('oferta.turma', fn($q) => $q->where('id_escola', $authUser->id_escola));
        }

        if ($request->agendados_search) {
            $agendadosQuery->whereHas('recurso', function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->agendados_search . '%');
            });
        }

        $agendadosSortBy = $request->input('agendados_sort_by', 'data_hora_inicio');
        $agendadosOrder = $request->input('agendados_order', 'asc');

        if ($agendadosSortBy === 'recurso.nome' || $agendadosSortBy === 'recurso.quantidade') {
            $agendadosQuery->join('recursos_didaticos', 'agendamentos.id_recurso', '=', 'recursos_didaticos.id_recurso');
            $sortField = ($agendadosSortBy === 'recurso.nome') ? 'recursos_didaticos.nome' : 'recursos_didaticos.quantidade';
            $agendadosQuery->orderBy($sortField, $agendadosOrder);
        } elseif ($agendadosSortBy === 'oferta.turma.serie') {
            $agendadosQuery->join('oferta_componentes', 'agendamentos.id_oferta', '=', 'oferta_componentes.id_oferta')
                    ->join('turmas', 'oferta_componentes.id_turma', '=', 'turmas.id_turma')
                    ->orderBy('turmas.serie', $agendadosOrder);
        } elseif ($agendadosSortBy === 'oferta.professor.nome_completo') {
            $agendadosQuery->join('oferta_componentes', 'agendamentos.id_oferta', '=', 'oferta_componentes.id_oferta')
                    ->join('usuarios', 'oferta_componentes.id_professor', '=', 'usuarios.id_usuario')
                    ->orderBy('usuarios.nome_completo', $agendadosOrder);
        } else {
            $agendadosQuery->orderBy($agendadosSortBy, $agendadosOrder);
        }

        $agendadosPaginados = $agendadosQuery->select('agendamentos.*')->paginate(5, ['*'], 'agendados_page');

        $agendadosPaginados->getCollection()->transform(function ($agendamento) use ($authUser) {
            $podeCancelar = false;

            if ($authUser->tipo_usuario === 'administrador') {
                $podeCancelar = true;
            }

            elseif ($agendamento->oferta && $agendamento->oferta->professor && $agendamento->oferta->turma) {
                
                if ($authUser->id_usuario == $agendamento->oferta->id_professor) {
                    $podeCancelar = true;
                }
                
                elseif ($authUser->tipo_usuario === 'diretor' && $authUser->id_escola == $agendamento->oferta->turma->id_escola) {
                    $podeCancelar = true;
                }
            }

            $agendamento->can_cancel = $podeCancelar;
            return $agendamento;
        });

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
        $titulo = 'Novo Agendamento Realizado';
        $mensagemTemplate = "Novo agendamento do recurso '{recurso_nome}' para {data_hora}, solicitado por {professor_nome}.";
        $this->notifyRelatedUsers($agendamento, $titulo, $mensagemTemplate);
        return response()->json(['message' => 'Agendamento criado com sucesso!'], 201);
    }


    public function destroy(Agendamento $agendamento)
    {
        $agendamento->load(['oferta.professor', 'oferta.turma', 'recurso']);

        // Gate::authorize('cancelar-agendamento', $agendamento);

        if (now()->diffInMinutes($agendamento->data_hora_inicio, false) < 10) {
            return response()->json(['message' => 'Agendamentos não podem ser cancelados com menos de 10 minutos de antecedência do seu início para fins de log do sistema.'], 422);
        }

        $titulo = 'Agendamento Cancelado';
        $autorAcao = Auth::user()->nome_completo;
        $mensagemTemplate = "O agendamento do recurso '{recurso_nome}' para {data_hora} (de {professor_nome}) foi cancelado por {$autorAcao}.";
        $this->notifyRelatedUsers($agendamento, $titulo, $mensagemTemplate);
        $agendamento->delete();
        return response()->json(['message' => 'Agendamento cancelado com sucesso.']);
    }

    /**
    *
    * @param Agendamento $agendamento
    * @param string $titulo
    * @param string $mensagemTemplate
    */
    private function notifyRelatedUsers(Agendamento $agendamento, string $titulo, string $mensagemTemplate)
    {
        if (!$agendamento->oferta || !$agendamento->oferta->professor || !$agendamento->oferta->turma) {
            return;
        }

        $professor = $agendamento->oferta->professor;
        $escolaId = $agendamento->oferta->turma->id_escola;
        $admins = Usuario::where('tipo_usuario', 'administrador')->get();
        $diretor = Usuario::where('tipo_usuario', 'diretor')
                            ->where('id_escola', $escolaId)
                            ->first();

        $usersToNotify = collect($admins);
        if ($diretor) {
            $usersToNotify->push($diretor);
        }

        $usersToNotify->push($professor);
        $uniqueUsers = $usersToNotify->unique('id_usuario');
        $recursoNome = $agendamento->recurso->nome;
        $agendamentoData = Carbon::parse($agendamento->data_hora_inicio)->format('d/m/Y \à\s H:i');
        
        foreach ($uniqueUsers as $user) {
            $mensagem = str_replace(
                ['{recurso_nome}', '{professor_nome}', '{data_hora}'],
                [$recursoNome, $professor->nome_completo, $agendamentoData],
                $mensagemTemplate
            );

            Notificacao::create([
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $user->id_usuario
            ]);

            if ($user->preferencias && $user->preferencias->notif_email && $user->email) {
                try {
                    Mail::to($user->email)->send(new NotificationMail($titulo, $mensagem));
                } catch (\Exception $e) {
                    Log::error("Falha ao enviar e-mail de notificação para {$user->email}: " . $e->getMessage());
                }
            }
        }
    }
}