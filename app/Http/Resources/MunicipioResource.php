<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MunicipioResource extends JsonResource
{
    /**
     * 
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_municipio,
            'nome' => $this->nome,
            'tipo' => $this->tipo,
            'escolas' => EscolaResource::collection($this->whenLoaded('escolas')),
        ];
    }
}