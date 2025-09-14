<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agendamento>
 */
class AgendamentoFactory extends Factory
{
    /**
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::instance($this->faker->dateTimeBetween('now', '+1 month'));
        $endDate = $startDate->copy()->addHours($this->faker->numberBetween(1, 3));

        return [
            'data_hora_inicio' => $startDate,
            'data_hora_fim' => $endDate,
            'status' => $this->faker->randomElement(['agendado', 'livre']),
        ];
    }
}