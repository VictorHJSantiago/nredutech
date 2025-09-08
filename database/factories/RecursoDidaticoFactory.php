<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RecursoDidatico;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecursoDidatico>
 */
class RecursoDidaticoFactory extends Factory
{
    /**
     *
     * @var string
     */
    protected $model = RecursoDidatico::class;

    /**
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->words(3, true),
            'tipo' => $this->faker->randomElement(['didatico', 'laboratorio']),
            'marca' => $this->faker->company,
            'numero_serie' => $this->faker->unique()->ean13(),
            'quantidade' => $this->faker->numberBetween(1, 100),
            'observacoes' => $this->faker->sentence(),
            'data_aquisicao' => $this->faker->date(),
            'status' => $this->faker->randomElement(['funcionando', 'em_manutencao', 'quebrado', 'descartado']),
        ];
    }
}
