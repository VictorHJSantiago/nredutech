<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password; 

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome_completo' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:'.Usuario::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.Usuario::class],
            'tipo_usuario' => ['required', 'string', Rule::in(['administrador', 'diretor', 'professor'])],
            'status_aprovacao' => ['required', 'string', Rule::in(['ativo', 'pendente', 'bloqueado'])],
            'data_nascimento' => ['nullable', 'date'],
            'cpf' => ['nullable', 'string', 'max:14', 'unique:'.Usuario::class],
            'rg' => ['nullable', 'string', 'max:20', 'unique:'.Usuario::class],
            'rco_siape' => ['nullable', 'string', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'formacao' => ['nullable', 'string', 'max:255'],
            'area_formacao' => ['nullable', 'string', 'max:255'],

            'password' => 'required|string|min:8|confirmed',

        ];
    }
}
