<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Usuario;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    /**
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'username' => ['required', 'string', 'max:80', 'unique:usuarios,username'],
            'data_nascimento' => ['nullable', 'date_format:Y-m-d'],
            'cpf' => ['nullable', 'string', 'max:14', 'unique:usuarios,cpf'],
            'rg' => ['nullable', 'string', 'max:20', 'unique:usuarios,rg'],
            'rco_siape' => ['nullable', 'string', 'max:50', 'unique:usuarios,rco_siape'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'formacao' => ['nullable', 'string', 'max:255'],
            'area_formacao' => ['nullable', 'string', 'max:255'],
            'tipo_usuario' => ['required', 'in:diretor,professor'], 
            'id_escola' => [
                'required', /
                'exists:escolas,id_escola',
                function ($attribute, $value, $fail) {
                    $tipo = $this->input('tipo_usuario');

                    if ($tipo === 'diretor') {
                        $directorCount = Usuario::where('id_escola', $value)
                                                ->where('tipo_usuario', 'diretor')
                                                ->count();
                        if ($directorCount >= 2) {
                            $fail('Esta escola já atingiu o limite de 2 (dois) diretores cadastrados. O cadastro ficará pendente de revisão do administrador.');
                        }
                    }

                    if ($tipo === 'professor') {
                        $profCount = Usuario::where('id_escola', $value)
                                            ->where('tipo_usuario', 'professor')
                                            ->count();
                        if ($profCount >= 3) {
                            $fail('Esta escola já atingiu o limite de 3 (três) professores. O cadastro ficará pendente de revisão.');
                        }
                    }
                },
            ],
        ];
    }
}

