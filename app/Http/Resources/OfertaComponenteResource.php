<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfertaComponenteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_oferta,
            'turma' => new TurmaResource($this->whenLoaded('turma')),
            'professor' => new UsuarioResource($this->whenLoaded('professor')),
            'componente' => new ComponenteCurricularResource($this->whenLoaded('componenteCurricular')),
            'agendamentos' => AgendamentoResource::collection($this->whenLoaded('agendamentos')),
        ];
    }
}