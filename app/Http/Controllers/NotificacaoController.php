<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function destroy(Notificacao $notificacao): RedirectResponse
    {
        if ($notificacao->id_usuario !== Auth::id()) {
            abort(403);
        }

        $notificacao->delete();

        return redirect()->route('notifications.index')->with('success', 'Notificação removida com sucesso.');
    }

    public function clearAll(): RedirectResponse
    {
        Notificacao::where('id_usuario', Auth::id())->delete();

        return redirect()->route('notifications.index')->with('success', 'Todas as suas notificações foram limpas.');
    }
}