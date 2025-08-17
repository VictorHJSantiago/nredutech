<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecursoDidaticoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_recurso,
            'nome' => $this->nome,
            'marca' => $this->marca,
            'numeroSerie' => $this->numero_serie,
            'quantidade' => $this->quantidade,
            'observacoes' => $this->observacoes,
            'dataUltimaLimpeza' => $this->data_ultima_limpeza,
            'status' => $this->status,
            'agendamentos' => AgendamentoResource::collection($this->whenLoaded('agendamentos')),
        ];
    }
}