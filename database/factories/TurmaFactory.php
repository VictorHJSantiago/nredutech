<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Escola;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Turma>
 */
class TurmaFactory extends Factory
{
    /**
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'serie' => $this->faker->randomElement(['6º Ano A', '7º Ano B', '8º Ano A', '9º Ano C', '1º Ano A', '2º Ano B', '3º Ano C']),
            'turno' => $this->faker->randomElement(['manha', 'tarde', 'noite']),
            'ano_letivo' => $this->faker->numberBetween(date('Y') - 1, date('Y') + 1),
            'nivel_escolaridade' => $this->faker->randomElement(['fundamental_2', 'medio']),
            'id_escola' => Escola::inRandomOrder()->first()->id_escola, 
        ];
    }
}