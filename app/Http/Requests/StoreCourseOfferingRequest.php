<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseOfferingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_turma' => 'required|exists:turmas,id_turma',
            'id_professor' => 'required|exists:usuarios,id_usuario',
            'id_componente' => 'required|exists:componentes_curriculares,id_componente',
        ];
    }
}