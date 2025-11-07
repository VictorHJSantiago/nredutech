<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; 
use App\Mail\NotificationMail;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Usuario::query()->with('escola');

        if ($user->tipo_usuario === 'diretor' && $user->id_escola) {
            $query->where('id_escola', $user->id_escola)
                  ->where('tipo_usuario', '!=', 'administrador');
        }

        $query->when($request->query('search'), function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('nome_completo', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
            });
        });
        
        $query->when($request->query('status'), function ($q, $status) {
            return $q->where('status_aprovacao', $status);
        });

        $query->when($request->query('search_doc'), function ($q, $searchDoc) {
            return $q->where(function ($subQ) use ($searchDoc) {
                $subQ->where('cpf', 'like', "%{$searchDoc}%")
                     ->orWhere('rg', 'like', "%{$searchDoc}%")
                     ->orWhere('rco_siape', 'like', "%{$searchDoc}%");
            });
        });
        $query->when($request->query('search_edu'), function ($q, $searchEdu) {
            return $q->where(function ($subQ) use ($searchEdu) {
                $subQ->where('formacao', 'like', "%{$searchEdu}%")
                     ->orWhere('area_formacao', 'like', "%{$searchEdu}%");
            });
        });
        $query->when($request->query('search_date'), function ($q, $searchDate) {
             return $q->where(function ($subQ) use ($searchDate) {
                 $subQ->whereDate('data_registro', $searchDate)
                      ->orWhereDate('data_nascimento', $searchDate);
             });
        });

        $sortBy = $request->query('sort_by', 'nome_completo');
        $order = $request->query('order', 'asc');

        $allowedSorts = [
            'id_usuario',
            'nome_completo',
            'email',
            'data_registro',
            'tipo_usuario',
            'status_aprovacao',
            'data_nascimento',
            'cpf',
            'rg',
            'rco_siape',
            'telefone',
            'formacao',
            'area_formacao'
        ];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $order);
        } else {
            $query->orderBy('nome_completo', 'asc');
        }
        $usuarios = $query->paginate(5)->withQueryString();
        return view('users.index', compact('usuarios', 'sortBy', 'order'));
    }

    public function create(): View
    {
        $userAutenticado = Auth::user();
        $perfilUsuarioLogado = Usuario::where('email', $userAutenticado->email)->first();

        $escolas = collect();

        if ($perfilUsuarioLogado && $perfilUsuarioLogado->tipo_usuario === 'administrador') {
            $escolas = Escola::orderBy('nome')->get();
        }
        elseif ($perfilUsuarioLogado && $perfilUsuarioLogado->tipo_usuario === 'diretor' && $perfilUsuarioLogado->id_escola) {
            $escolas = Escola::where('id_escola', $perfilUsuarioLogado->id_escola)->get();
        }

        return view('users.create', compact('escolas'));
    }

      public function store(StoreUserRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $validatedData['data_registro'] = now();
        $validatedData['password'] = Hash::make($validatedData['password']);
        $usuario = Usuario::create($validatedData);
        
        $actor = Auth::user();
        $administradores = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
        $diretoresDaEscola = collect();
        if ($usuario->id_escola) {
            $diretoresDaEscola = Usuario::where('id_escola', $usuario->id_escola)
                                        ->where('tipo_usuario', 'diretor')
                                        ->where('status_aprovacao', 'ativo')
                                        ->get();
        }
        $recipients = collect([$actor])->merge($administradores)->merge($diretoresDaEscola)->unique('id_usuario');

        $titulo = 'Novo Usuário Cadastrado Manualmente';
        $mensagem = "O usuário '{$usuario->nome_completo}' (Status: {$usuario->status_aprovacao}) foi cadastrado no sistema por {$actor->nome_completo}.";

        foreach ($recipients as $recipient) {
            Notificacao::create([
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $recipient->id_usuario,
            ]);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuário cadastrado com sucesso!');
    }


    public function show(Usuario $usuario): View
    {
        $usuario->load(['escola', 'preferencias', 'notificacoes']);
        return view('users.index', compact('usuario'));
    }

    public function edit(Usuario $usuario): View|RedirectResponse
    {
        $userAutenticado = Auth::user();
        if ($userAutenticado->tipo_usuario === 'administrador' && $usuario->tipo_usuario === 'administrador' && $userAutenticado->id_usuario !== $usuario->id_usuario && $usuario->status_aprovacao === 'ativo') {
            return redirect()->route('usuarios.index')->with('error', 'Administradores não podem editar outros administradores ativos.');
        }

        $perfilUsuarioLogado = Usuario::where('email', $userAutenticado->email)->first();

        $escolas = collect();

        if ($perfilUsuarioLogado && $perfilUsuarioLogado->tipo_usuario === 'administrador') {
            $escolas = Escola::orderBy('nome')->get();
        } elseif ($perfilUsuarioLogado && $perfilUsuarioLogado->tipo_usuario === 'diretor' && $perfilUsuarioLogado->id_escola) {
            $escolas = Escola::where('id_escola', $perfilUsuarioLogado->id_escola)->get();
        }

        return view('users.edit', compact('usuario', 'escolas'));
    }

     public function update(UpdateUserRequest $request, Usuario $usuario): RedirectResponse
    {
        $actor = Auth::user();
        if ($actor->tipo_usuario === 'administrador' && $usuario->tipo_usuario === 'administrador' && $actor->id_usuario !== $usuario->id_usuario && $usuario->status_aprovacao === 'ativo') {
            return redirect()->route('usuarios.index')->with('error', 'Administradores não podem editar outros administradores ativos.');
        }

        $validatedData = $request->validated();
        
        $oldEscolaId = $usuario->id_escola;
        $statusChanged = isset($validatedData['status_aprovacao']) && $usuario->status_aprovacao !== $validatedData['status_aprovacao'];
        $statusNovo = $validatedData['status_aprovacao'] ?? $usuario->status_aprovacao;

        if ($actor->tipo_usuario === 'diretor') {
            unset($validatedData['tipo_usuario']);
            unset($validatedData['id_escola']);
        }

        $usuario->update($validatedData);
        $usuario->refresh()->loadMissing(['escola', 'preferencias']);
        $newEscolaId = $usuario->id_escola;
        $escolaNome = $usuario->escola ? $usuario->escola->nome : 'N/A';
        $administradores = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
        $diretoresNovaEscola = collect();
        if ($newEscolaId) {
            $diretoresNovaEscola = Usuario::where('id_escola', $newEscolaId)->where('tipo_usuario', 'diretor')->where('status_aprovacao', 'ativo')->get();
        }
        $diretoresAntigaEscola = collect();
        if ($oldEscolaId && $oldEscolaId !== $newEscolaId) {
            $diretoresAntigaEscola = Usuario::where('id_escola', $oldEscolaId)->where('tipo_usuario', 'diretor')->where('status_aprovacao', 'ativo')->get();
        }

        $recipients = collect([$actor, $usuario])
                        ->merge($administradores)
                        ->merge($diretoresNovaEscola)
                        ->merge($diretoresAntigaEscola)
                        ->unique('id_usuario');
        
        $mensagem = "O usuário '{$usuario->nome_completo}' (Escola: {$escolaNome}) foi atualizado por {$actor->nome_completo}.";
        if ($statusChanged) {
            $mensagem .= " Seu status mudou para '{$statusNovo}'.";
        }

        foreach ($recipients as $recipient) {
            if ($recipient->id_usuario === $usuario->id_usuario && $statusChanged) {
                continue;
            }
            
            Notificacao::create([
                'titulo' => 'Usuário Atualizado',
                'mensagem' => $mensagem,
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $recipient->id_usuario,
            ]);
        }

        if ($statusChanged) {
            $tituloStatus = 'Status da Sua Conta Atualizado';
            $mensagemStatus = "O status da sua conta foi atualizado para '{$statusNovo}' pelo usuário {$actor->nome_completo}.";

            Notificacao::create([
                'titulo' => $tituloStatus,
                'mensagem' => $mensagemStatus,
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $usuario->id_usuario,
            ]);
            
            if ($usuario->preferencias && $usuario->preferencias->notif_email) {
                Mail::to($usuario->email)->send(new NotificationMail($tituloStatus, $mensagemStatus));
            }
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(Usuario $usuario): RedirectResponse
    {
        $actor = Auth::user();

        if ($usuario->id_usuario === $actor->id_usuario) {
            return redirect()->route('usuarios.index')->with('error', 'Você não pode excluir sua própria conta.');
        }

        if ($actor->tipo_usuario === 'diretor' && $usuario->tipo_usuario === 'administrador') {
            return redirect()->route('usuarios.index')->with('error', 'Você não tem permissão para excluir este usuário.');
        }

        if ($actor->tipo_usuario === 'diretor' && $usuario->id_escola !== $actor->id_escola) {
            return redirect()->route('usuarios.index')->with('error', 'Você só pode excluir usuários da sua própria escola.');
        }

        $nomeUsuario = $usuario->nome_completo;
        $escolaId = $usuario->id_escola;
        
        $diretores = collect();
        if ($escolaId) {
            $diretores = Usuario::where('id_escola', $escolaId)
                                  ->where('tipo_usuario', 'diretor')
                                  ->where('status_aprovacao', 'ativo')
                                  ->get();
        }
        $administradores = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
        
        $recipients = collect([$actor])->merge($diretores)->merge($administradores)->unique('id_usuario');
        
        $usuario->delete();

        $titulo = 'Usuário Excluído';
        $mensagem = "O usuário '{$nomeUsuario}' foi removido do sistema por {$actor->nome_completo}.";

        foreach ($recipients as $recipient) {
             Notificacao::create([
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $recipient->id_usuario,
            ]);
        }
        
        return redirect()->route('usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }
}