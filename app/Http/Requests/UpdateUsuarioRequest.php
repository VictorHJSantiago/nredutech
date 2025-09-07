<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Usuario; 

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('usuario')->id_usuario;
        $idEscolaRequest = $this->input('id_escola');

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
            
            'tipo_usuario' => [
                'sometimes',
                'required',
                Rule::in(['administrador', 'diretor', 'professor']),
                function ($attribute, $value, $fail) use ($idEscolaRequest, $userId) {
                        if ($value === 'administrador') {
                        $adminCount = Usuario::where('tipo_usuario', 'administrador')
                                             ->where('id_usuario', '!=', $userId) 
                                             ->count();
                        if ($adminCount >= 6) {
                            $fail('O limite total de (5) administradores adicionais já foi atingido.');
                        }
                    }

                    if (in_array($value, ['diretor', 'professor']) && $idEscolaRequest) {
                        
                        $query = Usuario::where('id_escola', $idEscolaRequest)
                                        ->where('id_usuario', '!=', $userId); // Exclui o próprio usuário
                                                if ($value === 'diretor') {
                            $diretorCount = (clone $query)->where('tipo_usuario', 'diretor')->count();
                            if ($diretorCount >= 2) {
                                $fail('Esta instituição já atingiu o limite de (2) diretores.');
                            }
                        }

                        if ($value === 'professor') {
                            $professorCount = (clone $query)->where('tipo_usuario', 'professor')->count();
                            if ($professorCount >= 3) {
                                $fail('Esta instituição já atingiu o limite de (3) professores.');
                            }
                        }
                    }
                }
            ],
            
            'id_escola' => 'nullable|exists:escolas,id_escola',
        ];
    }
}