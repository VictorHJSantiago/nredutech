<?php

namespace App\Http\Requests;

use App\Models\Agendamento;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAgendamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data_hora_inicio' => ['sometimes', 'required', 'date', 'after:' . now()->addHours(3)->toDateTimeString()],
            'data_hora_fim' => 'sometimes|required|date|after:data_hora_inicio',
            'status' => 'sometimes|required|in:agendado,livre',
            'id_recurso' => ['sometimes', 'required', 'exists:recursos_didaticos,id_recurso', $this->regraConflito()],
            'id_oferta' => 'sometimes|required|exists:oferta_componentes,id_oferta',
        ];
    }

    private function regraConflito()
    {
        return function ($attribute, $value, $fail) {
            $inicio = $this->input('data_hora_inicio');
            $fim = $this->input('data_hora_fim');
            $agendamentoId = $this->route('agendamento')->id_agendamento;

            $conflito = Agendamento::where('id_recurso', $value)
                ->where('id_agendamento', '!=', $agendamentoId) 
                ->where(fn($q) => $q->where('data_hora_inicio', '<', $fim)->where('data_hora_fim', '>', $inicio))
                ->exists();

            if ($conflito) {
                $fail('Este recurso já está agendado para o período selecionado.');
            }
        };
    }
}