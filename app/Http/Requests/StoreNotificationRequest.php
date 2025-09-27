<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'mensagem' => 'required|string',
            'status_mensagem' => 'required|in:enviada,lida',
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_agendamento' => 'nullable|exists:agendamentos,id_agendamento',
        ];
    }
}