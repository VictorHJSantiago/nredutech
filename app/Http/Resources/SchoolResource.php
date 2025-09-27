<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_escola,
            'nome' => $this->nome,
            'endereco' => $this->endereco,
            'tipo' => $this->tipo,
            'municipio' => new CityResource($this->whenLoaded('municipio')),
            'diretor' => new UserResource($this->whenLoaded('diretor')),
            'turmas' => SchoolClassResource::collection($this->whenLoaded('turmas')),
            'usuarios' => UserResource::collection($this->whenLoaded('usuarios')),
        ];
    }
}