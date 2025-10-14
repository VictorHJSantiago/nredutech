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
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $nomeCompleto = "{$firstName} {$lastName}";
        $username = strtolower(Str::slug("{$firstName}.{$lastName}", '.')).$this->faker->randomNumber(3);
        $email = "{$username}@example.com";

        $formacoes = [
            'Licenciatura em Pedagogia' => 'Ciências Humanas',
            'Licenciatura em Letras' => 'Linguagens',
            'Licenciatura em Matemática' => 'Ciências Exatas',
            'Licenciatura em História' => 'Ciências Humanas',
            'Licenciatura em Geografia' => 'Ciências Humanas',
            'Licenciatura em Ciências Biológicas' => 'Ciências Biológicas',
            'Licenciatura em Física' => 'Ciências Exatas',
            'Licenciatura em Química' => 'Ciências Exatas',
            'Licenciatura em Artes Visuais' => 'Artes',
            'Licenciatura em Educação Física' => 'Ciências da Saúde',
        ];
        $formacao = $this->faker->randomElement(array_keys($formacoes));
        $areaFormacao = $formacoes[$formacao];

        return [
            'nome_completo' => $nomeCompleto,
            'username' => $username,
            'email' => $email,
            'password' => static::$password ??= Hash::make('Password@12345678'),
            'data_nascimento' => $this->faker->date('Y-m-d', '2000-01-01'),
            'cpf' => $this->faker->unique()->cpf(false),
            'rg' => $this->faker->unique()->rg(false),
            'rco_siape' => $this->faker->unique()->numerify('SIAPE#######'),
            'telefone' => $this->faker->unique()->cellphoneNumber(false),
            'formacao' => $formacao,
            'area_formacao' => $areaFormacao,
            'data_registro' => now(),
            'status_aprovacao' => $this->faker->randomElement(['ativo', 'pendente', 'bloqueado']),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
}