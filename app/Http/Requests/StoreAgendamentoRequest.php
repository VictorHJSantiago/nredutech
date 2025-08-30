<?php

namespace App\Http\Requests;

use App\Models\Agendamento;
use Illuminate\Foundation\Http\FormRequest;

class StoreAgendamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data_hora_inicio' => ['required', 'date', 'after:' . now()->addHours(3)->toDateTimeString()],
            'data_hora_fim' => 'required|date|after:data_hora_inicio',
            'status' => 'required|in:agendado,livre',
            'id_recurso' => ['required', 'exists:recursos_didaticos,id_recurso', $this->regraConflito()],
            'id_oferta' => 'required|exists:oferta_componentes,id_oferta',
        ];
    }

    private function regraConflito()
    {
        return function ($attribute, $value, $fail) {
            $inicio = $this->input('data_hora_inicio');
            $fim = $this->input('data_hora_fim');

            $conflito = Agendamento::where('id_recurso', $value)
                ->where(fn($q) => $q->where('data_hora_inicio', '<', $fim)->where('data_hora_fim', '>', $inicio))
                ->exists();

            if ($conflito) {
                $fail('Este recurso já está agendado para o período selecionado.');
            }
        };
    }
}