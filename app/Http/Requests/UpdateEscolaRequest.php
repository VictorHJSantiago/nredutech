<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEscolaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'sometimes|required|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'id_municipio' => 'sometimes|required|exists:municipios,id_municipio',
            'id_diretor_responsavel' => 'nullable|exists:usuarios,id_usuario',
            'tipo' => 'sometimes|required|in:colegio_estadual,escola_tecnica,escola_municipal',
        ];
    }
}