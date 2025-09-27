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


class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = Usuario::query()->with('escola');
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
        foreach ($administradores as $admin) {
            Notificacao::create([
                'titulo' => 'Novo Usuário Cadastrado Manualmente',
                'mensagem' => "O usuário '{$usuario->nome_completo}' foi cadastrado no sistema e está com status '{$usuario->status_aprovacao}'.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $admin->id_usuario,
            ]);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuário cadastrado com sucesso!');
    }


    public function show(Usuario $usuario): View
    {
        $usuario->load(['escola', 'preferencias', 'notificacoes']);
        return view('users.index', compact('usuario')); 
    }

    public function edit(Usuario $usuario): View
    {
        $userAutenticado = Auth::user();
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
        $validatedData = $request->validated();
        
        if (isset($validatedData['status_aprovacao']) && $usuario->status_aprovacao !== $validatedData['status_aprovacao']) {
            $status = $validatedData['status_aprovacao'];
            Notificacao::create([
                'titulo' => 'Status da Sua Conta Atualizado',
                'mensagem' => "O status da sua conta foi atualizado para '{$status}'.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $usuario->id_usuario,
            ]);
        }

        $usuario->update($validatedData);
        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(Usuario $usuario): RedirectResponse
    {
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

        foreach ($usersToNotify as $user) {
             Notificacao::create([
                'titulo' => 'Usuário Excluído',
                'mensagem' => "O usuário '{$nomeUsuario}' foi removido do sistema.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $user->id_usuario,
            ]);
        }
        
        return redirect()->route('usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }
}