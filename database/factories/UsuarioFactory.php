<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nome_completo' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'data_nascimento' => fake()->date('Y-m-d', '2000-01-01'),
            'cpf' => fake()->unique()->numerify('###.###.###-##'),
            'rg' => fake()->unique()->numerify('1#.###.###-#'),
            'rco_siape' => fake()->unique()->numerify('SIA#######'),
            'telefone' => fake()->phoneNumber(),
            'formacao' => fake()->randomElement(['Licenciatura em Pedagogia', 'Licenciatura em Matemática', 'Licenciatura em História', 'Ciência da Computação']),
            'area_formacao' => fake()->randomElement(['Ciências Humanas', 'Ciências Exatas', 'Linguagens', 'Ciências da Natureza']),
            'data_registro' => now(),
            'status_aprovacao' => fake()->randomElement(['ativo', 'pendente', 'bloqueado']),
            'tipo_usuario' => fake()->randomElement(['professor', 'diretor']), 
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}