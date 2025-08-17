<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EscolaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_escola,
            'nome' => $this->nome,
            'endereco' => $this->endereco,
            'tipo' => $this->tipo,
            'municipio' => new MunicipioResource($this->whenLoaded('municipio')),
            'diretor' => new UsuarioResource($this->whenLoaded('diretor')),
            'turmas' => TurmaResource::collection($this->whenLoaded('turmas')),
            'usuarios' => UsuarioResource::collection($this->whenLoaded('usuarios')),
        ];
    }
}