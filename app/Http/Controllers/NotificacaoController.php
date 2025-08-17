<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificacaoRequest;
use App\Http\Requests\UpdateNotificacaoRequest;
use App\Http\Resources\NotificacaoResource;
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificacaoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        // $query = Notificacao::query()->where('id_usuario', auth()->id());
        $query = Notificacao::query()->with(['usuario', 'agendamento']);

        $query->when($request->query('status'), function ($q, $status) {
            return $q->where('status_mensagem', $status);
        });

        $notificacoes = $query->latest('data_envio')->paginate(15);

        return NotificacaoResource::collection($notificacoes);
    }

    public function store(StoreNotificacaoRequest $request): NotificacaoResource
    {
        $validatedData = $request->validated();
        $validatedData['data_envio'] = now();
        
        $notificacao = Notificacao::create($validatedData);

        return new NotificacaoResource($notificacao->load(['usuario', 'agendamento']));
    }

    public function show(Notificacao $notificacao): NotificacaoResource
    {
        return new NotificacaoResource($notificacao->load(['usuario', 'agendamento']));
    }

    public function update(UpdateNotificacaoRequest $request, Notificacao $notificacao): NotificacaoResource
    {
        $notificacao->update($request->validated());

        return new NotificacaoResource($notificacao->fresh());
    }

    public function marcarComoLida(Notificacao $notificacao): NotificacaoResource
    {
        // $this->authorize('update', $notificacao);

        $notificacao->update(['status_mensagem' => 'lida']);
        return new NotificacaoResource($notificacao->fresh());
    }

    public function destroy(Notificacao $notificacao): JsonResponse
    {
        $notificacao->delete();

        return response()->json(null, 204);
    }
}