<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $usuario = Usuario::where('email', $user->email)->first();

        return [
            'nome_completo' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id), Rule::unique(Usuario::class)->ignore($usuario->id_usuario, 'id_usuario')],
            'username' => ['required', 'string', 'max:80', Rule::unique(Usuario::class)->ignore($usuario->id_usuario, 'id_usuario')],
            'telefone' => ['nullable', 'string', 'max:20'],
        ];
    }
}