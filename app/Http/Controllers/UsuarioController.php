<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\Usuario;
use App\Models\Escola; 
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash; 


class UsuarioController extends Controller
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
            'status_aprovacao'
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

     public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $validatedData['data_registro'] = now();
        
        $validatedData['password'] = Hash::make($validatedData['password']);
        
        Usuario::create($validatedData); 

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

    public function update(UpdateUsuarioRequest $request, Usuario $usuario): RedirectResponse
    {
        $userAutenticado = Auth::user();
        $perfilUsuarioLogado = Usuario::where('email', $userAutenticado->email)->first();
        $validatedData = $request->validated();
        
        if ($perfilUsuarioLogado && $perfilUsuarioLogado->tipo_usuario === 'diretor') {
            if($usuario->id_escola != $perfilUsuarioLogado->id_escola) {
                 return redirect()->route('usuarios.index')->with('error', 'Acesso não autorizado.');
            }
            $validatedData['id_escola'] = $perfilUsuarioLogado->id_escola;
        }

        $usuario->update($validatedData);

        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(Usuario $usuario): RedirectResponse
    {
        $userAutenticado = Auth::user();
        $perfilUsuarioLogado = Usuario::where('email', $userAutenticado->email)->first();

        if ($perfilUsuarioLogado && $perfilUsuarioLogado->tipo_usuario === 'diretor') {
            if($usuario->id_escola != $perfilUsuarioLogado->id_escola) {
                 return redirect()->route('usuarios.index')->with('error', 'Acesso não autorizado.');
            }
        }
        
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }
}
