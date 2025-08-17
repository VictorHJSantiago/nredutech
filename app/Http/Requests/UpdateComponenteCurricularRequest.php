<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComponenteCurricularRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'carga_horaria' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:pendente,aprovado,reprovado',
        ];
    }
}