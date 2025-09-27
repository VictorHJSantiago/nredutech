<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'serie' => 'required|string|max:50',
            'turno' => 'required|in:manha,tarde,noite',
            'ano_letivo' => 'required|integer|digits:4',
            'nivel_escolaridade' => 'required|in:fundamental_1,fundamental_2,medio',
            'id_escola' => 'required|exists:escolas,id_escola',
        ];
    }
}