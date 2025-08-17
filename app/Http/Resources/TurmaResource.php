<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TurmaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_turma,
            'serie' => $this->serie,
            'turno' => $this->turno,
            'anoLetivo' => $this->ano_letivo,
            'nivelEscolaridade' => $this->nivel_escolaridade,
            'escola' => new EscolaResource($this->whenLoaded('escola')),
            'ofertasComponentes' => OfertaComponenteResource::collection($this->whenLoaded('ofertasComponentes')),
        ];
    }
}