<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('usuario')->id_usuario;

        return [
            'nome_completo' => 'sometimes|required|string|max:255',
            'username' => ['sometimes', 'required', 'string', 'max:80', Rule::unique('usuarios')->ignore($userId, 'id_usuario')],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('usuarios')->ignore($userId, 'id_usuario')],
            'data_nascimento' => 'nullable|date_format:Y-m-d',
            'cpf' => ['nullable', 'string', 'max:14', Rule::unique('usuarios')->ignore($userId, 'id_usuario')],
            'rg' => ['nullable', 'string', 'max:20', Rule::unique('usuarios')->ignore($userId, 'id_usuario')],
            'rco_siape' => ['nullable', 'string', 'max:50', Rule::unique('usuarios')->ignore($userId, 'id_usuario')],
            'telefone' => 'nullable|string|max:20',
            'formacao' => 'nullable|string|max:255',
            'area_formacao' => 'nullable|string|max:255',
            'status_aprovacao' => 'sometimes|required|in:ativo,pendente,bloqueado',
            'tipo_usuario' => 'sometimes|required|in:administrador,diretor,professor',
            'id_escola' => 'nullable|exists:escolas,id_escola',
        ];
    }
}