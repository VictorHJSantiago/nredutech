<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseOfferingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_oferta,
            'turma' => new SchoolClassResource($this->whenLoaded('turma')),
            'professor' => new UserResource($this->whenLoaded('professor')),
            'componente' => new CurricularComponentResource($this->whenLoaded('componenteCurricular')),
            'agendamentos' => AppointmentResource::collection($this->whenLoaded('agendamentos')),
        ];
    }
}