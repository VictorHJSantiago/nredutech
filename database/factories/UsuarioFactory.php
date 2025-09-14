<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

class UsuarioFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $faker = FakerFactory::create('pt_BR');
        $nomeCompleto = $faker->name();

        return [
            'nome_completo' => $nomeCompleto,
            'username' => strtolower(str_replace([' ', '.'], '', $nomeCompleto)) . $faker->unique()->randomNumber(5),
            'email' => $faker->unique()->safeEmail(),
            'password' => static::$password ??= 'Password@12345678',
            'data_nascimento' => $faker->date('Y-m-d', '2000-01-01'),
            'cpf' => $faker->unique()->cpf(false),
            'rg' => $faker->unique()->rg(false),
            'rco_siape' => $faker->unique()->numerify('##########'),
            'telefone' => $faker->unique()->cellphoneNumber(false),
            'formacao' => $faker->randomElement(['Licenciatura', 'Bacharelado', 'Tecnólogo']),
            'area_formacao' => $faker->randomElement(['Ciências Humanas', 'Ciências Exatas', 'Ciências Biológicas']),
            'data_registro' => now(),
            'status_aprovacao' => 'ativo', 
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}