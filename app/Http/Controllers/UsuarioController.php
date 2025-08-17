<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        return response()->json(Usuario::with(['escola', 'preferencias'])->get());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome_completo' => 'required|string|max:255',
            'username' => 'required|string|max:80|unique:usuarios,username',
            'email' => 'required|email|max:255|unique:usuarios,email',
            'data_nascimento' => 'nullable|date',
            'cpf' => 'nullable|string|max:14|unique:usuarios,cpf',
            'status_aprovacao' => 'required|in:ativo,pendente,bloqueado',
            'tipo_usuario' => 'required|in:administrador,diretor,professor',
            'id_escola' => 'nullable|exists:escolas,id_escola',
        ]);
        
        $validatedData['data_registro'] = now();

        $usuario = Usuario::create($validatedData);

        return response()->json($usuario, 201);
    }

    public function show(Usuario $usuario)
    {
        return response()->json($usuario->load(['escola', 'preferencias', 'notificacoes']));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $validatedData = $request->validate([
            'nome_completo' => 'sometimes|required|string|max:255',
            'username' => ['sometimes', 'required', 'string', 'max:80', Rule::unique('usuarios')->ignore($usuario->id_usuario, 'id_usuario')],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('usuarios')->ignore($usuario->id_usuario, 'id_usuario')],
            'status_aprovacao' => 'sometimes|required|in:ativo,pendente,bloqueado',
            'tipo_usuario' => 'sometimes|required|in:administrador,diretor,professor',
            'id_escola' => 'nullable|exists:escolas,id_escola',
        ]);

        $usuario->update($validatedData);

        return response()->json($usuario);
    }

    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return response()->json(null, 204);
    }
}