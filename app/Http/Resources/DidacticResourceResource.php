<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DidacticResourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_recurso,
            'nome' => $this->nome,
            'tipo' => $this->tipo,
            'marca' => $this->marca,
            'numeroSerie' => $this->numero_serie,
            'quantidade' => $this->quantidade,
            'observacoes' => $this->observacoes,
            'dataAquisicao' => $this->data_aquisicao,
            'status' => $this->status,
        ];
    }
}