<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Usuario; 

class UpdateUserRequest extends FormRequest
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
            'id_escola' => [
                'nullable',
                'exists:escolas,id_escola',
                function ($attribute, $value, $fail) use ($userId) {
                    
                    $tipo_usuario = $this->input('tipo_usuario', $this->route('usuario')->tipo_usuario);

                    if (($tipo_usuario === 'diretor' || $tipo_usuario === 'professor') && !$value) {
                        $fail('O campo escola é obrigatório para diretores e professores.');
                    }

                    if ($tipo_usuario === 'administrador' && $value) {
                         $fail('Administradores não podem ser associados a uma escola.');
                    }

                    if ($tipo_usuario === 'administrador') {
                        $adminCount = Usuario::where('tipo_usuario', 'administrador')
                                             ->where('id_usuario', '!=', $userId)
                                             ->count();
                        if ($adminCount >= 5) {
                            $fail('O sistema já atingiu o limite de 5 (cinco) administradores globais.');
                        }
                    }

                    if ($value && $tipo_usuario === 'diretor') {
                        $directorCount = Usuario::where('id_escola', $value)
                                                ->where('tipo_usuario', 'diretor')
                                                ->where('id_usuario', '!=', $userId) 
                                                ->count();
                        
                        if ($directorCount >= 2) {
                            $fail('Esta escola já atingiu o limite de 2 (dois) diretores cadastrados.');
                        }
                    }

                    if ($value && $tipo_usuario === 'professor') {
                        $profCount = Usuario::where('id_escola', $value)
                                            ->where('tipo_usuario', 'professor')
                                            ->where('id_usuario', '!=', $userId) 
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