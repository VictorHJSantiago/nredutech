<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome_completo' => 'required|string|max:255',
            'username' => 'required|string|max:80|unique:usuarios,username',
            'email' => 'required|email|max:255|unique:usuarios,email',
            'data_nascimento' => 'nullable|date_format:Y-m-d',
            'cpf' => 'nullable|string|max:14|unique:usuarios,cpf',
            'rg' => 'nullable|string|max:20|unique:usuarios,rg',
            'rco_siape' => 'nullable|string|max:50|unique:usuarios,rco_siape',
            'telefone' => 'nullable|string|max:20',
            'formacao' => 'nullable|string|max:255',
            'area_formacao' => 'nullable|string|max:255',
            'status_aprovacao' => 'required|in:ativo,pendente,bloqueado',
            'tipo_usuario' => 'required|in:administrador,diretor,professor',
            'id_escola' => 'nullable|exists:escolas,id_escola',
        ];
    }
}