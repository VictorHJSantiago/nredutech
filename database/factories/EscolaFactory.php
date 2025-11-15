<?php

namespace Database\Factories;

use App\Models\Escola;
use App\Models\Municipio;
use Illuminate\Database\Eloquent\Factories\Factory;

class EscolaFactory extends Factory
{
    protected $model = Escola::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->company . ' Escola',
            'endereco' => $this->faker->address,
            'id_municipio' => Municipio::factory(),
            'id_diretor_responsavel' => null,
            'nivel_ensino' => $this->faker->randomElement(['Infantil', 'Fundamental', 'Médio']),
            'tipo' => $this->faker->randomElement(['Municipal', 'Estadual', 'Privada']),
        ];
    }
}