<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'serie' => 'sometimes|required|string|max:50',
            'turno' => 'sometimes|required|in:manha,tarde,noite',
            'ano_letivo' => 'sometimes|required|integer|digits:4',
            'nivel_escolaridade' => 'sometimes|required|in:fundamental_1,fundamental_2,medio',
            'id_escola' => 'sometimes|required|exists:escolas,id_escola',
        ];
    }
}