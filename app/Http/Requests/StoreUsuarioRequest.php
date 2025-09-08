<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Usuario; 
use Illuminate\Validation\Rule; 

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