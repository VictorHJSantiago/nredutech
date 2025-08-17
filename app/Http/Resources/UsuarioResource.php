<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_usuario,
            'nomeCompleto' => $this->nome_completo,
            'username' => $this->username,
            'email' => $this->email,
            'dataNascimento' => $this->data_nascimento,
            'telefone' => $this->telefone,
            'formacao' => $this->formacao,
            'dataRegistro' => $this->data_registro,
            'status' => $this->status_aprovacao,
            'tipo' => $this->tipo_usuario,
            'escola' => new EscolaResource($this->whenLoaded('escola')),
            'preferencias' => new UsuarioPreferenciaResource($this->whenLoaded('preferencias')),
            'notificacoes' => NotificacaoResource::collection($this->whenLoaded('notificacoes')),
            'ofertasComponentes' => OfertaComponenteResource::collection($this->whenLoaded('ofertasComponentes')),
        ];
    }
}