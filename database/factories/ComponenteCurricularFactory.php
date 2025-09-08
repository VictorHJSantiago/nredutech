<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ComponenteCurricular;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComponenteCurricular>
 */
class ComponenteCurricularFactory extends Factory
{
    /**
     *
     * @var string
     */
    protected $model = ComponenteCurricular::class;

    /**
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->randomElement(['Matemática', 'Português', 'História', 'Geografia', 'Ciências', 'Física', 'Química', 'Biologia', 'Artes', 'Educação Física']),
            'descricao' => $this->faker->paragraph,
            'carga_horaria' => $this->faker->randomElement(['40h', '60h', '80h', '120h']),
            'status' => $this->faker->randomElement(['pendente', 'aprovado', 'reprovado']),
        ];
    }
}
