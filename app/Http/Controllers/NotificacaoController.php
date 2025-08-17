<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use Illuminate\Http\Request;

class NotificacaoController extends Controller
{
    public function index()
    {
        // Notificacao::where('id_usuario', auth()->id())->get()
        return response()->json(Notificacao::with('usuario')->get());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'titulo' => 'required|string|max:255',
            'mensagem' => 'required|string',
            'status_mensagem' => 'required|in:enviada,lida',
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_agendamento' => 'nullable|exists:agendamentos,id_agendamento',
        ]);

        $validatedData['data_envio'] = now();
        $notificacao = Notificacao::create($validatedData);
        return response()->json($notificacao, 201);
    }

    public function show(Notificacao $notificacao)
    {
        return response()->json($notificacao);
    }

    public function update(Request $request, Notificacao $notificacao)
    {
        $validatedData = $request->validate([
            'status_mensagem' => 'sometimes|required|in:enviada,lida',
        ]);

        $notificacao->update($validatedData);
        return response()->json($notificacao);
    }

    public function destroy(Notificacao $notificacao)
    {
        $notificacao->delete();
        return response()->json(null, 204);
    }
}