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
        $series = [
            '6º Ano A', '6º Ano B',
            '7º Ano A', '7º Ano B',
            '8º Ano A', '8º Ano B',
            '9º Ano A', '9º Ano B',
            '1º Ano do Ensino Médio A', '1º Ano do Ensino Médio B',
            '2º Ano do Ensino Médio A', '2º Ano do Ensino Médio B',
            '3º Ano do Ensino Médio A', '3º Ano do Ensino Médio B',
        ];

        return [
            'serie' => $this->faker->randomElement($series),
            'turno' => $this->faker->randomElement(['manha', 'tarde', 'noite']),
            'ano_letivo' => $this->faker->numberBetween(date('Y') - 1, date('Y') + 1),
            'nivel_escolaridade' => $this->faker->randomElement(['fundamental_2', 'medio']),
            'id_escola' => Escola::inRandomOrder()->first()->id_escola,
        ];
    }
}