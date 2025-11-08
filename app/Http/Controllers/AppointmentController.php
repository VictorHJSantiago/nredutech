<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
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
use App\Models\Turma;
use Illuminate\Support\Facades\DB;

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
        
        $ofertas = $ofertasQuery->get()->sortBy('componenteCurricular.nome')->values();
        
        $ofertasJson = $ofertas->map(function($o) {
            return [
                'id_oferta' => $o->id_oferta,
                'turma' => ['serie' => $o->turma->serie ?? 'N/A'],
                'componente_curricular' => ['nome' => $o->componenteCurricular->nome ?? 'N/A'],
                'professor' => ['nome_completo' => $o->professor->nome_completo ?? 'N/A'],
            ];
        });

        $sortBy = $request->query('sort_by', 'data_hora_inicio');
        $order = $request->query('order', 'asc');
        $search = $request->query('search');
        $allowedSorts = ['recurso_nome', 'data_hora_inicio', 'turma_serie', 'recurso_quantidade', 'professor_nome', 'escola_nome'];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'data_hora_inicio';
        }
        if (!in_array(strtolower($order), ['asc', 'desc'])) {
            $order = 'asc';
        }

        $meusAgendamentosQuery = Agendamento::with(['recurso', 'oferta.turma.escola', 'oferta.professor'])
            ->select('agendamentos.*')
            ->join('oferta_componentes', 'agendamentos.id_oferta', '=', 'oferta_componentes.id_oferta')
            ->join('recursos_didaticos', 'agendamentos.id_recurso', '=', 'recursos_didaticos.id_recurso')
            ->join('turmas', 'oferta_componentes.id_turma', '=', 'turmas.id_turma')
            ->join('escolas', 'turmas.id_escola', '=', 'escolas.id_escola')
            ->join('usuarios', 'oferta_componentes.id_professor', '=', 'usuarios.id_usuario');


        if ($authUser->tipo_usuario === 'professor') {
             $meusAgendamentosQuery->where('oferta_componentes.id_professor', $authUser->id_usuario);
        } elseif ($authUser->tipo_usuario === 'diretor' && $authUser->id_escola) {
            $meusAgendamentosQuery->where('turmas.id_escola', $authUser->id_escola);
        }
            
        $meusAgendamentosQuery->where('data_hora_inicio', '>=', now());

        $meusAgendamentosQuery->when($search, function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('recursos_didaticos.nome', 'like', "%{$search}%")
                     ->orWhere('recursos_didaticos.quantidade', 'like', "%{$search}%")
                     ->orWhere('usuarios.nome_completo', 'like', "%{$search}%")
                     ->orWhere('escolas.nome', 'like', "%{$search}%")
                     ->orWhere('agendamentos.data_hora_inicio', 'like', "%{$search}%");
            });
        });

        $sortColumn = $sortBy;
        if ($sortBy === 'recurso_nome') {
            $sortColumn = 'recursos_didaticos.nome';
        } elseif ($sortBy === 'recurso_quantidade') {
            $sortColumn = 'recursos_didaticos.quantidade';
        } elseif ($sortBy === 'turma_serie') {
            $sortColumn = 'turmas.serie';
        } elseif ($sortBy === 'professor_nome') { 
            $sortColumn = 'usuarios.nome_completo';
        } elseif ($sortBy === 'escola_nome') { 
            $sortColumn = 'escolas.nome';
        }
        
        $meusAgendamentosQuery->orderBy($sortColumn, $order);


        $meusAgendamentos = $meusAgendamentosQuery->paginate(5, ['*'], 'meus_agendamentos_page')->withQueryString();

        return view('appointments.index', [
            'ofertas' => $ofertas,
            'ofertasJson' => $ofertasJson,
            'meusAgendamentos' => $meusAgendamentos,
            'now' => now()->toIso8601String(),
            'sortBy' => $sortBy,
            'order' => $order,
            'search' => $search
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
        $request->validate(['date' => 'required|date_format:Y-m-d']);
        $date = Carbon::parse($request->date)->setTimezone(config('app.timezone'));
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();
        $authUser = Auth::user();

        $agendamentosDoDiaQuery = Agendamento::query()->whereBetween('data_hora_inicio', [$startOfDay, $endOfDay]);
        if ($authUser->tipo_usuario !== 'administrador' && $authUser->id_escola) {
            $agendamentosDoDiaQuery->whereHas('oferta.turma', fn($q) => $q->where('id_escola', $authUser->id_escola));
        }

        $recursosDisponiveisQuery = RecursoDidatico::query()
            ->with(['escola', 'criador'])
            ->leftJoin('usuarios', 'recursos_didaticos.id_usuario_criador', '=', 'usuarios.id_usuario')
            ->leftJoin('escolas', 'recursos_didaticos.id_escola', '=', 'escolas.id_escola')
            ->where('recursos_didaticos.status', 'funcionando')
            ->select('recursos_didaticos.*', 'usuarios.nome_completo as criador_nome', 'escolas.nome as escola_nome');

        if ($authUser->tipo_usuario !== 'administrador') {
            $userSchoolId = $authUser->id_escola;
            $recursosDisponiveisQuery->where(function ($q) use ($userSchoolId) {
                if ($userSchoolId) {
                    $q->where('recursos_didaticos.id_escola', $userSchoolId);
                }
                $q->orWhereNull('recursos_didaticos.id_escola');
            });
        }

        if ($request->disponiveis_search) {
            $search = $request->disponiveis_search;
            $recursosDisponiveisQuery->where(function ($q) use ($search) {
                $q->where('recursos_didaticos.nome', 'like', "%{$search}%")
                  ->orWhere('recursos_didaticos.quantidade', 'like', "%{$search}%")
                  ->orWhere('usuarios.nome_completo', 'like', "%{$search}%")
                  ->orWhere('escolas.nome', 'like', "%{$search}%");
            });
        }

        $disponiveisSortBy = $request->input('disponiveis_sort_by', 'nome');
        $disponiveisOrder = $request->input('disponiveis_order', 'asc');
        
        $sortFieldDisp = match($disponiveisSortBy) {
            'criador_nome' => 'usuarios.nome_completo',
            'escola_nome' => 'escolas.nome',
            'quantidade' => 'recursos_didaticos.quantidade',
            default => 'recursos_didaticos.nome'
        };
        
        $recursosDisponiveisQuery->orderBy($sortFieldDisp, $disponiveisOrder);
        $recursosDisponiveis = $recursosDisponiveisQuery->paginate(5, ['*'], 'disponiveis_page');

        $agendadosQuery = Agendamento::with(['recurso', 'oferta.turma.escola', 'oferta.professor', 'oferta.componenteCurricular'])
            ->whereBetween('agendamentos.data_hora_inicio', [$startOfDay, $endOfDay]);

        if ($authUser->tipo_usuario !== 'administrador' && $authUser->id_escola) {
            $agendadosQuery->whereHas('oferta.turma', fn($q) => $q->where('id_escola', $authUser->id_escola));
        }
        
        $agendadosQuery->join('recursos_didaticos', 'agendamentos.id_recurso', '=', 'recursos_didaticos.id_recurso')
                                ->join('oferta_componentes', 'agendamentos.id_oferta', '=', 'oferta_componentes.id_oferta')
                                ->join('usuarios', 'oferta_componentes.id_professor', '=', 'usuarios.id_usuario')
                                ->join('turmas', 'oferta_componentes.id_turma', '=', 'turmas.id_turma')
                                ->join('escolas', 'turmas.id_escola', '=', 'escolas.id_escola');

        if ($request->agendados_search) {
            $search = $request->agendados_search;
            $agendadosQuery->where(function ($q) use ($search) {
                $q->where('recursos_didaticos.nome', 'like', "%{$search}%")
                  ->orWhere('recursos_didaticos.quantidade', 'like', "%{$search}%")
                  ->orWhere('usuarios.nome_completo', 'like', "%{$search}%")
                  ->orWhere('escolas.nome', 'like', "%{$search}%")
                  ->orWhere('agendamentos.data_hora_inicio', 'like', "%{$search}%");
            });
        }

        $agendadosSortBy = $request->input('agendados_sort_by', 'data_hora_inicio');
        $agendadosOrder = $request->input('agendados_order', 'asc');

        $sortFieldAg = match($agendadosSortBy) {
            'recurso.nome' => 'recursos_didaticos.nome',
            'recurso.quantidade' => 'recursos_didaticos.quantidade',
            'oferta.turma.serie' => 'turmas.serie',
            'oferta.professor.nome_completo' => 'usuarios.nome_completo',
            'escola_nome' => 'escolas.nome',
            default => 'agendamentos.data_hora_inicio'
        };

        $agendadosQuery->orderBy($sortFieldAg, $agendadosOrder);

        $agendadosPaginados = $agendadosQuery->select('agendamentos.*')->paginate(5, ['*'], 'agendados_page');

        $agendadosPaginados->getCollection()->transform(function ($agendamento) use ($authUser) {
            $podeCancelar = false;
            if ($authUser->tipo_usuario === 'administrador') {
                $podeCancelar = true;
            } elseif ($agendamento->oferta && $agendamento->oferta->professor && $agendamento->oferta->turma) {
                if ($authUser->id_usuario == $agendamento->oferta->id_professor) {
                    $podeCancelar = true;
                } elseif ($authUser->tipo_usuario === 'diretor' && $authUser->id_escola == $agendamento->oferta->turma->id_escola) {
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
        $user = Auth::user();
        $inicio = Carbon::parse($validatedData['data_hora_inicio']);
        $fim = Carbon::parse($validatedData['data_hora_fim']);
        
        $recurso = RecursoDidatico::where('id_recurso', $request->id_recurso)
                                     ->where('status', 'funcionando')
                                     ->firstOrFail();

        $this->authorizeResourceAccess($recurso);

        $oferta = OfertaComponente::findOrFail($request->id_oferta);
        if ($user->tipo_usuario === 'professor' && $oferta->id_professor !== $user->id_usuario) {
             abort(403, 'Professores só podem agendar em suas próprias ofertas.');
        }

        if ($inicio->hour >= 23 || $inicio->hour < 6) {
            return response()->json(['message' => 'Não é permitido criar agendamentos entre 23:00 e 06:00.'], 422);
        }
        
        $conflito = Agendamento::where('id_recurso', $validatedData['id_recurso'])
            ->where('status', 'agendado')
            ->where('data_hora_inicio', '<', $fim)
            ->where('data_hora_fim', '>', $inicio)
            ->exists();

        if ($conflito) {
            return response()->json(['message' => 'Este recurso já está agendado para este horário. Por favor, escolha outro horário.'], 422);
        }

        $validatedData['status'] = 'agendado';
        $agendamento = Agendamento::create($validatedData);
        $agendamento->loadMissing(['oferta.turma.escola', 'recurso', 'oferta.professor']);
        $titulo = 'Novo Agendamento Realizado';
        $mensagemTemplate = "Novo agendamento do recurso '{recurso_nome}' para {data_hora}, solicitado por {professor_nome}.";
        
        dispatch(function () use ($agendamento, $titulo, $mensagemTemplate) {
            if (!$agendamento->oferta || !$agendamento->oferta->professor || !$agendamento->oferta->turma || !$agendamento->recurso) {
                Log::warning("Faltando relações para notificar agendamento ID: {$agendamento->id_agendamento}");
                return;
            }

            $professor = $agendamento->oferta->professor;
            $escolaId = $agendamento->oferta->turma->id_escola;
            
            $admins = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
            $diretores = collect();
            if ($escolaId) {
                 $diretores = Usuario::where('tipo_usuario', 'diretor')
                                       ->where('id_escola', $escolaId)
                                       ->where('status_aprovacao', 'ativo')
                                       ->get();
            }

            $usersToNotify = collect($admins)->merge($diretores)->push($professor)->unique('id_usuario');
            
            $recursoNome = $agendamento->recurso->nome;
            $agendamentoData = Carbon::parse($agendamento->data_hora_inicio)->format('d/m/Y \à\s H:i');
            
            $notificationsData = [];
            $now = now();
            
            foreach ($usersToNotify as $user) {
                $mensagem = str_replace(
                    ['{recurso_nome}', '{professor_nome}', '{data_hora}'],
                    [$recursoNome, $professor->nome_completo, $agendamentoData],
                    $mensagemTemplate
                );

                $notificationsData[] = [
                    'titulo' => $titulo,
                    'mensagem' => $mensagem,
                    'data_envio' => $now,
                    'status_mensagem' => 'enviada',
                    'id_usuario' => $user->id_usuario,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($user->preferencias && $user->preferencias->notif_email && $user->email) {
                    try {
                        Mail::to($user->email)->queue(new NotificationMail($titulo, $mensagem));
                    } catch (\Exception $e) {
                        Log::error("Falha ao enfileirar e-mail de notificação para {$user->email}: " . $e->getMessage());
                    }
                }
            }
            
            if (!empty($notificationsData)) {
                Notificacao::insert($notificationsData);
            }
        })->afterCommit(); 
        
        return response()->json(['message' => 'Agendamento criado com sucesso!'], 201);
    }

    public function destroy(Agendamento $agendamento)
    {
        Gate::authorize('cancelar-agendamento', $agendamento);
        $user = Auth::user();
        
        if (now()->diffInMinutes($agendamento->data_hora_inicio, false) > -10) {
             if (now()->diffInMinutes($agendamento->data_hora_inicio, false) < 10 && $agendamento->data_hora_inicio > now()) {
                 return response()->json(['message' => 'Agendamentos não podem ser cancelados com menos de 10 minutos de antecedência do seu início.'], 422);
             }
        }

        $agendamento->load(['oferta.professor', 'oferta.turma', 'recurso']);
        $titulo = 'Agendamento Cancelado';
        $autorAcao = $user->nome_completo;
        $mensagemTemplate = "O agendamento do recurso '{recurso_nome}' para {data_hora} (de {professor_nome}) foi cancelado por {$autorAcao}.";
        $agendamentoData = $agendamento->toArray(); 
        $agendamentoData['recurso'] = $agendamento->recurso->toArray();
        $agendamentoData['oferta']['professor'] = $agendamento->oferta->professor->toArray();
        $agendamentoData['oferta']['turma'] = $agendamento->oferta->turma->toArray();
        
        $agendamento->delete(); 

        dispatch(function () use ($agendamentoData, $titulo, $mensagemTemplate) {            
            $professor = (object) $agendamentoData['oferta']['professor'];
            $escolaId = $agendamentoData['oferta']['turma']['id_escola'];
            $recursoNome = $agendamentoData['recurso']['nome'];
            $dataInicioFormatada = Carbon::parse($agendamentoData['data_hora_inicio'])->format('d/m/Y \à\s H:i');

            $admins = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
            $diretores = collect();
            if ($escolaId) {
                 $diretores = Usuario::where('tipo_usuario', 'diretor')
                                       ->where('id_escola', $escolaId)
                                       ->where('status_aprovacao', 'ativo')
                                       ->get();
            }

            $professorModel = Usuario::find($professor->id_usuario);
            if (!$professorModel) {
                Log::warning("Professor não encontrado para notificação de cancelamento.");
                return;
            }

            $usersToNotify = collect($admins)->merge($diretores)->push($professorModel)->unique('id_usuario');
            
            $notificationsData = [];
            $now = now();
            
            foreach ($usersToNotify as $user) {
                $mensagem = str_replace(
                    ['{recurso_nome}', '{professor_nome}', '{data_hora}'],
                    [$recursoNome, $professor->nome_completo, $dataInicioFormatada],
                    $mensagemTemplate
                );

                $notificationsData[] = [
                    'titulo' => $titulo,
                    'mensagem' => $mensagem,
                    'data_envio' => $now,
                    'status_mensagem' => 'enviada',
                    'id_usuario' => $user->id_usuario,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($user->preferencias && $user->preferencias->notif_email && $user->email) {
                    try {
                        Mail::to($user->email)->queue(new NotificationMail($titulo, $mensagem));
                    } catch (\Exception $e) {
                        Log::error("Falha ao enfileirar e-mail de notificação para {$user->email}: " . $e->getMessage());
                    }
                }
            }
            
            if (!empty($notificationsData)) {
                Notificacao::insert($notificationsData);
            }
        }); 
        
        return response()->json(['message' => 'Agendamento cancelado com sucesso.']);
    }

    private function authorizeResourceAccess(RecursoDidatico $recurso)
    {
        $user = Auth::user();
        if ($user->tipo_usuario === 'administrador') {
            return;
        }

        $recurso->loadMissing('escola');
        
        if ($user->id_escola && ($recurso->id_escola === null || $recurso->id_escola === $user->id_escola)) {
            return;
        }
        
        if (!$user->id_escola && $recurso->id_escola === null) {
            return;
        }

        abort(403, 'Acesso não autorizado a este recurso.');
    }
}