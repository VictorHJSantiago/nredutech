<?php

namespace Database\Factories; 

use App\Models\Usuario; 
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Crypt; 

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     *
     * @var string
     */
    protected $model = Usuario::class; 

    /**
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome_completo' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'data_nascimento' => $this->faker->date(),
            
            // 2. Passe os dados em texto puro (sem Crypt::encryptString)
            'cpf' => $this->faker->numerify('###########'),
            'rg' => $this->faker->numerify('#########'),
            'rco_siape' => $this->faker->unique()->numerify('#######'),
            'telefone' => $this->faker->phoneNumber(),

            'formacao' => 'Superior Completo',
            'area_formacao' => 'Educação',
            'data_registro' => now(),
            'status_aprovacao' => 'ativo',
            'tipo_usuario' => 'aluno', 
            'id_escola' => null, 
            'password' => Hash::make('ValidPassword@123456'), 
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Define o estado para um usuário administrador.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function administrador(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo_usuario' => 'administrador',
                'id_escola' => null,
            ];
        });
    }

    /**
     * Define o estado para um usuário diretor.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function diretor(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo_usuario' => 'diretor',
            ];
        });
    }

    /**
     * Define o estado para um usuário professor.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function professor(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo_usuario' => 'professor',
            ];
        });
    }
}