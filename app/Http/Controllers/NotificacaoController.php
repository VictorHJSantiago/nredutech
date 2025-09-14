<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateNotificacaoRequest;
use App\Http\Resources\NotificacaoResource;
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificacaoController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $notificacoes = Notificacao::where('id_usuario', $user->id_usuario)
                                    ->latest('data_envio')
                                    ->paginate(15);
        
        Notificacao::where('id_usuario', $user->id_usuario)
                 ->where('status_mensagem', 'enviada')
                 ->update(['status_mensagem' => 'lida']);

        return view('notifications.index', compact('notificacoes'));
    }

    public function update(UpdateNotificacaoRequest $request, Notificacao $notificacao): NotificacaoResource
    {
        $notificacao->update($request->validated());
        return new NotificacaoResource($notificacao->fresh());
    }

    public function destroy(Notificacao $notificacao): JsonResponse
    {
        $notificacao->delete();
        return response()->json(null, 204);
    }
}