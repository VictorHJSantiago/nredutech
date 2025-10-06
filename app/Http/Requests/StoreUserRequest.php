<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Usuario;
use App\Rules\RgValido;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

/**
 * @property string $data_nascimento
 * @property string $tipo_usuario
 */
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
        return [
            'nome_completo' => 'required|string|max:255',
            'username' => 'required|string|max:80|unique:usuarios,username',
            'email' => 'required|email|max:255|unique:usuarios,email',
            'data_nascimento' => 'required|date_format:Y-m-d|after:1930-12-31|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'cpf' => ['required', 'cpf', 'unique:usuarios,cpf'],
            'rg' => ['required', new RgValido, 'unique:usuarios,rg'],
            'rco_siape' => 'required|string|max:50|unique:usuarios,rco_siape',
            'telefone' => 'required|celular_com_ddd',
            'formacao' => 'required|string|max:255',
            'area_formacao' => 'required|string|max:255',
            'status_aprovacao' => 'required|in:ativo,pendente,bloqueado',
            'tipo_usuario' => 'required|in:administrador,diretor,professor',
            'password' => ['required', 'confirmed', Password::min(16)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
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

    public function messages(): array
    {
        return [
            'data_nascimento.after' => 'O ano de nascimento deve ser posterior a 1930.',
            'data_nascimento.before_or_equal' => 'O usuário deve ter pelo menos 18 anos de idade.',
            'data_nascimento.date_format' => 'O formato da data de nascimento é inválido.',
            'password' => 'A senha deve ter no mínimo 16 caracteres e conter letras maiúsculas, minúsculas, números e símbolos.'
        ];
    }
}