<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'id_municipio' => 'required|exists:municipios,id_municipio',
            'id_diretor_responsavel' => 'nullable|exists:usuarios,id_usuario',
            'nivel_ensino' => 'required|in:colegio_estadual,escola_tecnica,escola_municipal',
            'tipo' => 'required|in:urbana,rural', 
        ];
    }
}