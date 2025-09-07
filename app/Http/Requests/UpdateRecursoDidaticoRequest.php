<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecursoDidaticoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        $recursoId = $this->route('recurso_didatico')->id_recurso;
        return [
            'nome' => 'sometimes|required|string|max:255',
            'tipo' => 'sometimes|required|in:didatico,laboratorio', 
            'marca' => 'nullable|string|max:100',
            'numero_serie' => ['nullable', 'string', 'max:100', Rule::unique('recursos_didaticos')->ignore($recursoId, 'id_recurso')],
            'quantidade' => 'sometimes|required|integer|min:1',
            'observacoes' => 'nullable|string',
            'data_aquisicao' => 'nullable|date_format:Y-m-d', 
            'status' => 'sometimes|required|in:funcionando,em_manutencao,quebrado,descartado',
        ];
    }
}
