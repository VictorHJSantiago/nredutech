<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'usuarioId' => $this->id_usuario,
            'notificacaoEmail' => (bool) $this->notif_email,
            'notificacaoPopup' => (bool) $this->notif_popup,
            'tema' => $this->tema,
            'tamanhoFonte' => $this->tamanho_fonte,
        ];
    }
}