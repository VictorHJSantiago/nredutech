<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $query = Usuario::query()->with('escola');

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
        return view('users.create');
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $validatedData['data_registro'] = now();
        
        Usuario::create($validatedData);

        return redirect()->route('usuarios.index')->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function show(Usuario $usuario): View
    {
        $usuario->load(['escola', 'preferencias', 'notificacoes']);
        return view('users.index', compact('usuario')); // Opcional, pode-se usar a página de edição.
    }

    public function edit(Usuario $usuario): View
    {
        return view('users.edit', compact('usuario'));
    }

    public function update(UpdateUsuarioRequest $request, Usuario $usuario): RedirectResponse
    {
        $usuario->update($request->validated());

        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(Usuario $usuario): RedirectResponse
    {
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }
}