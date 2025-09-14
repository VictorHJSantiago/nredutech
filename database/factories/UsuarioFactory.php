<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nome_completo' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('Password@12345678'),
            'data_nascimento' => $this->faker->date('Y-m-d', '2000-01-01'),
            'cpf' => $this->faker->unique()->cpf(false),
            'rg' => $this->faker->unique()->rg(false),
            'rco_siape' => $this->faker->unique()->numerify('SIAPE#######'),
            'telefone' => $this->faker->unique()->cellphoneNumber(false),
            'formacao' => $this->faker->jobTitle(),
            'area_formacao' => $this->faker->randomElement(['Ciências Humanas', 'Ciências Exatas', 'Ciências Biológicas', 'Linguagens', 'Artes']),
            'data_registro' => now(),
            'status_aprovacao' => $this->faker->randomElement(['ativo', 'pendente', 'bloqueado']),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
}