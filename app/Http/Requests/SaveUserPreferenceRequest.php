<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveUserPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notif_email' => 'required|boolean',
            'notif_popup' => 'required|boolean',
            'tema' => 'required|in:claro,escuro',
            'tamanho_fonte' => 'required|in:padrao,medio,grande',
            'id_usuario' => 'sometimes|required|exists:usuarios,id_usuario' 
        ];
    }
}