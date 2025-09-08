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

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $userAutenticado = Auth::user();
        $perfilUsuarioLogado = Usuario::where('email', $userAutenticado->email)->first();

        $query = Usuario::query()->with('escola');
        if ($perfilUsuarioLogado && $perfilUsuarioLogado->tipo_usuario === 'diretor') {
            $query->where('id_escola', $perfilUsuarioLogado->id_escola);
        }

        $query->when($request->query('status'), function ($q, $status) {
            return $q->where('status_aprovacao', $status);
        });

        $query->when($request->query('search'), function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('nome_completo', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
            });
        });
        
        $usuarios = $query->orderBy('nome_completo')->paginate(15)->withQueryString();

        return view('users.index', compact('usuarios'));
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
        
        $userAutenticado = Auth::user();
        $perfilUsuarioLogado = Usuario::where('email', $userAutenticado->email)->first();
        
        if ($perfilUsuarioLogado && $perfilUsuarioLogado->tipo_usuario === 'diretor') {
            $validatedData['id_escola'] = $perfilUsuarioLogado->id_escola;
        }
        
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
