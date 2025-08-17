<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificacaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_notificacao,
            'titulo' => $this->titulo,
            'mensagem' => $this->mensagem,
            'dataEnvio' => $this->data_envio,
            'status' => $this->status_mensagem,
            'usuario' => new UsuarioResource($this->whenLoaded('usuario')),
            'agendamento' => new AgendamentoResource($this->whenLoaded('agendamento')),
        ];
    }
}