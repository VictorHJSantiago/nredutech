<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; 

class StoreCurricularComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'carga_horaria' => 'required|string',
        ];

        if (Auth::user()->tipo_usuario === 'administrador') {
            $rules['id_escola'] = 'nullable|exists:escolas,id_escola';
            $rules['status'] = 'required|in:pendente,aprovado,reprovado'; 
        } else {
        }


        return $rules;
    }
}