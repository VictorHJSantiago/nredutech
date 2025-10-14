<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ComponenteCurricular;
use App\Models\Usuario;

class ComponenteCurricularFactory extends Factory
{
    protected $model = ComponenteCurricular::class;

    public function definition(): array
    {
        $disciplinas = [
            'Matemática' => 'Estudo de cálculo, álgebra e geometria.',
            'Português' => 'Análise da gramática e literatura.',
            'História' => 'Estudo dos eventos passados da humanidade.',
            'Geografia' => 'Estudo da superfície terrestre e suas características.',
            'Ciências' => 'Introdução à Biologia, Física e Química.',
            'Física' => 'Estudo dos fenômenos naturais e das propriedades da matéria.',
            'Química' => 'Estudo da composição e propriedades da matéria.',
            'Biologia' => 'Estudo dos seres vivos e seus processos vitais.',
            'Artes' => 'Exploração de formas de expressão visual e plástica.',
            'Filosofia' => 'Discussão sobre questões fundamentais da existência.',
            'Sociologia' => 'Estudo das estruturas e relações sociais.',
            'Inglês' => 'Aprendizado da língua inglesa.',
            'Programação' => 'Introdução à lógica e desenvolvimento de algoritmos.',
        ];
        
        $baseNome = $this->faker->randomElement(array_keys($disciplinas));
        $descricao = $disciplinas[$baseNome];
        $nome = $baseNome . ' ' . $this->faker->unique()->numberBetween(1000, 9999);

        return [
            'nome' => $nome,
            'descricao' => $descricao,
            'carga_horaria' => $this->faker->randomElement(['60h', '80h', '120h']),
            'status' => $this->faker->randomElement(['pendente', 'aprovado']),
            'id_usuario_criador' => Usuario::inRandomOrder()->first()->id_usuario ?? null,
        ];
    }
}