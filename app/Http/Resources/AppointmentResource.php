<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_agendamento,
            'dataHoraInicio' => $this->data_hora_inicio,
            'dataHoraFim' => $this->data_hora_fim,
            'status' => $this->status,
            'recurso' => new DidacticResourceResource($this->whenLoaded('recurso')),
            'oferta' => new CourseOfferingResource($this->whenLoaded('oferta')),
        ];
    }
}