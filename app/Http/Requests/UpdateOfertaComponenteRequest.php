<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfertaComponenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_turma' => 'sometimes|required|exists:turmas,id_turma',
            'id_professor' => 'sometimes|required|exists:usuarios,id_usuario',
            'id_componente' => 'sometimes|required|exists:componentes_curriculares,id_componente',
        ];
    }
}