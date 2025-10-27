<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Escola;
use App\Models\Turma; 

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Turma>
 */
class TurmaFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Turma::class;

    /**
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $seriesFundamental = [
            '6º Ano A', '6º Ano B',
            '7º Ano A', '7º Ano B',
            '8º Ano A', '8º Ano B',
            '9º Ano A', '9º Ano B',
        ];
        $seriesMedio = [
            '1º Ano A', '1º Ano B',
            '2º Ano A', '2º Ano B',
            '3º Ano A', '3º Ano B',
        ];

        $nivel = $this->faker->randomElement(['fundamental_2', 'medio']);
        $serie = ($nivel === 'fundamental_2')
            ? $this->faker->randomElement($seriesFundamental)
            : $this->faker->randomElement($seriesMedio);

        $escola = Escola::inRandomOrder()->first();
        if (!$escola) {
             throw new \Exception('Nenhuma escola encontrada no banco de dados para criar a turma.');
        }

        return [
            'serie' => $serie,
            'turno' => $this->faker->randomElement(['manha', 'tarde', 'noite']),
            'ano_letivo' => $this->faker->numberBetween(date('Y') - 1, date('Y')), 
            'nivel_escolaridade' => $nivel,
            'id_escola' => $escola->id_escola,
        ];
    }
}