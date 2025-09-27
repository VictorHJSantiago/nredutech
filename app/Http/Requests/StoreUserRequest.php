<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Usuario;
use App\Rules\RgValido;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
            'data_nascimento' => 'required|date_format:Y-m-d',
            'cpf' => ['required', 'cpf', 'unique:usuarios,cpf'],
            'rg' => ['required', new RgValido, 'unique:usuarios,rg'],
            'rco_siape' => 'required|string|max:50|unique:usuarios,rco_siape',
            'telefone' => 'required|celular_com_ddd',
            'formacao' => 'required|string|max:255',
            'area_formacao' => 'required|string|max:255',
            'status_aprovacao' => 'required|in:ativo,pendente,bloqueado',
            'tipo_usuario' => 'required|in:administrador,diretor,professor',
            'password' => ['required', 'confirmed', Password::min(16)->letters()->mixedCase()->numbers()->symbols()->uncompromised(), 'unique:usuarios,password'],
            'id_escola' => [
                'nullable',
                'exists:escolas,id_escola',
                function ($attribute, $value, $fail) {
                    $tipo_usuario = $this->input('tipo_usuario');

                    if (($tipo_usuario === 'diretor' || $tipo_usuario === 'professor') && !$value) {
                        $fail('O campo escola é obrigatório para diretores e professores.');
                    }
                    if ($tipo_usuario === 'administrador' && $value) {
                         $fail('Administradores não podem ser associados a uma escola.');
                    }
                    if ($tipo_usuario === 'administrador') {
                        $adminCount = Usuario::where('tipo_usuario', 'administrador')->count();
                        if ($adminCount >= 5) {
                            $fail('O sistema já atingiu o limite de 5 (cinco) administradores globais.');
                        }
                    }
                    if ($value && $tipo_usuario === 'diretor') {
                        $directorCount = Usuario::where('id_escola', $value)
                                                ->where('tipo_usuario', 'diretor')
                                                ->count();

                        if ($directorCount >= 2) {
                            $fail('Esta escola já atingiu o limite de 2 (dois) diretores cadastrados.');
                        }
                    }
                    if ($value && $tipo_usuario === 'professor') {
                        $profCount = Usuario::where('id_escola', $value)
                                            ->where('tipo_usuario', 'professor')
                                            ->count();

                        if ($profCount >= 3) {
                            $fail('Esta escola já atingiu o limite de 3 (três) professores cadastrados.');
                        }
                    }
                },
            ],
        ];
    }
}