<?php

namespace Database\Factories; // <-- O namespace correto é Database\Factories

use App\Models\Usuario; // <-- Importa o seu modelo
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * O nome do modelo correspondente da factory.
     *
     * @var string
     */
    protected $model = Usuario::class; // <-- Aponta para a classe do modelo

    /**
     * Define o estado padrão do modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Definição para gerar dados falsos
        return [
            'nome_completo' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'data_nascimento' => $this->faker->date(),
            'cpf' => $this->faker->numerify('###########'),
            'rg' => $this->faker->numerify('#########'),
            'rco_siape' => $this->faker->unique()->numerify('#######'),
            'telefone' => $this->faker->phoneNumber(),
            'formacao' => 'Superior Completo',
            'area_formacao' => 'Educação',
            'data_registro' => now(),
            'status_aprovacao' => 'ativo',
            'tipo_usuario' => 'aluno', // ou 'professor'
            'id_escola' => null, // Defina um valor padrão se necessário
            'password' => Hash::make('password'), // Senha padrão 
            'remember_token' => Str::random(10),
        ];
    }
}