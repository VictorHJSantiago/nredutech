<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use App\Rules\RgValido;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

/**
 * @mixin \Illuminate\Http\Request
 * @property \App\Models\Usuario $usuario
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('data_nascimento')) {
            try {
                $this->merge([
                    'data_nascimento' => Carbon::parse($this->data_nascimento)->format('Y-m-d'),
                ]);
            } catch (\Exception $e) {
            }
        }
    }

    public function rules(): array
    {
        $userId = $this->route('usuario')->id_usuario;

        return [
            'nome_completo' => 'sometimes|required|string|max:255',
            'username' => ['sometimes', 'required', 'string', 'max:80', Rule::unique('usuarios')->ignore($userId, 'id_usuario')],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('usuarios')->ignore($userId, 'id_usuario')],
            'data_nascimento' => 'sometimes|required|date_format:Y-m-d|after:1930-12-31|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'cpf' => ['sometimes', 'required', 'cpf', Rule::unique('usuarios')->ignore($userId, 'id_usuario')],
            'rg' => ['sometimes', 'required', new RgValido, Rule::unique('usuarios')->ignore($userId, 'id_usuario')],
            'rco_siape' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('usuarios')->ignore($userId, 'id_usuario')],
            'telefone' => 'sometimes|required|celular_com_ddd',
            'formacao' => 'sometimes|required|string|max:255',
            'area_formacao' => 'sometimes|required|string|max:255',
            
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

                    if ($value && $tipo_usuario === 'diretor' && $this->input('status_aprovacao') === 'ativo') {
                        $directorCount = Usuario::where('id_escola', $value)
                                                ->where('tipo_usuario', 'diretor')
                                                ->where('status_aprovacao', 'ativo')
                                                ->where('id_usuario', '!=', $userId) 
                                                ->count();
                        
                        if ($directorCount >= 2) {
                            $fail('Esta escola já atingiu o limite de 2 (dois) diretores ativos cadastrados.');
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

    public function messages(): array
    {
        return [
            'data_nascimento.after' => 'O ano de nascimento deve ser posterior a 1930.',
            'data_nascimento.before_or_equal' => 'O usuário deve ter pelo menos 18 anos de idade.',
            'data_nascimento.date_format' => 'O formato da data de nascimento é inválido.',
        ];
    }
}
