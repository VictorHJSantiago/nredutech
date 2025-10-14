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
        $administradores = Usuario::where('tipo_usuario', 'administrador')->get();

        $titulo = 'Novo Usuário Cadastrado Manualmente';
        $mensagem = "O usuário '{$usuario->nome_completo}' foi cadastrado no sistema e está com status '{$usuario->status_aprovacao}'.";

        foreach ($administradores as $admin) {
            Notificacao::create([
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $admin->id_usuario,
            ]);

            if ($admin->preferencias && $admin->preferencias->notif_email) {
                Mail::to($admin->email)->send(new NotificationMail($titulo, $mensagem));
            }
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
        $authUser = Auth::user();
        if ($authUser->tipo_usuario === 'administrador' && $usuario->tipo_usuario === 'administrador' && $authUser->id_usuario !== $usuario->id_usuario && $usuario->status_aprovacao === 'ativo') {
            return redirect()->route('usuarios.index')->with('error', 'Administradores não podem editar outros administradores ativos.');
        }

        $validatedData = $request->validated();
        
        if (Auth::user()->tipo_usuario === 'diretor') {
            unset($validatedData['tipo_usuario']);
            unset($validatedData['id_escola']);
        }

        if (isset($validatedData['status_aprovacao']) && $usuario->status_aprovacao !== $validatedData['status_aprovacao']) {
            $status = $validatedData['status_aprovacao'];
            $titulo = 'Status da Sua Conta Atualizado';
            $mensagem = "O status da sua conta foi atualizado para '{$status}'.";

            Notificacao::create([
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $usuario->id_usuario,
            ]);
            
            $usuario->load('preferencias'); 
            if ($usuario->preferencias && $usuario->preferencias->notif_email) {
                Mail::to($usuario->email)->send(new NotificationMail($titulo, $mensagem));
            }
        }

        $usuario->update($validatedData);
        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(Usuario $usuario): RedirectResponse
    {
        $authUser = Auth::user();

        if ($usuario->id_usuario === $authUser->id_usuario) {
            return redirect()->route('usuarios.index')->with('error', 'Você não pode excluir sua própria conta.');
        }

        if ($authUser->tipo_usuario === 'diretor' && $usuario->tipo_usuario === 'administrador') {
            return redirect()->route('usuarios.index')->with('error', 'Você não tem permissão para excluir este usuário.');
        }

        if ($authUser->tipo_usuario === 'diretor' && $usuario->id_escola !== $authUser->id_escola) {
            return redirect()->route('usuarios.index')->with('error', 'Você só pode excluir usuários da sua própria escola.');
        }

        $nomeUsuario = $usuario->nome_completo;
        $escolaId = $usuario->id_escola;
        $usuario->delete();
        $diretores = collect();
        if ($escolaId) {
            $diretores = Usuario::where('id_escola', $escolaId)
                                ->where('tipo_usuario', 'diretor')
                                ->get();
        }
        $administradores = Usuario::where('tipo_usuario', 'administrador')->get();
        
        $usersToNotify = $diretores->merge($administradores)->unique('id_usuario');

        $titulo = 'Usuário Excluído';
        $mensagem = "O usuário '{$nomeUsuario}' foi removido do sistema.";

        foreach ($usersToNotify as $user) {
             Notificacao::create([
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $user->id_usuario,
            ]);

            if ($user->preferencias && $user->preferencias->notif_email) {
                Mail::to($user->email)->send(new NotificationMail($titulo, $mensagem));
            }
        }
        
        return redirect()->route('usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }
}