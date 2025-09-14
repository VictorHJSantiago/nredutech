<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Turma;
use App\Models\Usuario;
use App\Models\ComponenteCurricular;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfertaComponente>
 */
class OfertaComponenteFactory extends Factory
{
    /**
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_turma' => Turma::inRandomOrder()->first()->id_turma,
            'id_professor' => Usuario::where('tipo_usuario', 'professor')->inRandomOrder()->first()->id_usuario,
            'id_componente' => ComponenteCurricular::inRandomOrder()->first()->id_componente,
        ];
    }
}