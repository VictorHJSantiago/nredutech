<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecursoDidaticoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:didatico,laboratorio', 
            'marca' => 'nullable|string|max:100',
            'numero_serie' => 'nullable|string|max:100|unique:recursos_didaticos,numero_serie',
            'quantidade' => 'required|integer|min:1',
            'observacoes' => 'nullable|string',
            'data_aquisicao' => 'nullable|date_format:Y-m-d',
            'status' => 'required|in:funcionando,em_manutencao,quebrado,descartado',
        ];
    }
}
