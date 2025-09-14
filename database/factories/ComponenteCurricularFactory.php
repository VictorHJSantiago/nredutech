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
            'Matemática Avançada' => 'Estudo de cálculo diferencial e integral, álgebra linear e equações diferenciais.',
            'Língua Portuguesa e Literatura' => 'Análise aprofundada da gramática normativa e estudo dos principais movimentos da literatura brasileira e portuguesa.',
            'História do Brasil' => 'Análise do período colonial, império e república, com foco nos aspectos sociais, políticos e econômicos.',
            'Geografia Mundial' => 'Estudo da geopolítica contemporânea, globalização, blocos econômicos e questões ambientais.',
            'Ciências da Natureza' => 'Introdução aos conceitos fundamentais de Biologia, Física e Química para o ensino fundamental.',
            'Física Moderna' => 'Abordagem da teoria da relatividade, mecânica quântica e física de partículas.',
            'Química Orgânica' => 'Estudo das estruturas, propriedades e reações dos compostos de carbono.',
            'Biologia Celular' => 'Análise das estruturas e funções das células, o bloco de construção da vida.',
            'Artes Visuais' => 'Exploração prática e teórica da pintura, escultura, desenho e história da arte.',
            'Filosofia e Sociologia' => 'Discussão sobre as correntes do pensamento filosófico e as estruturas sociais ao longo da história.',
        ];
        $nome = $this->faker->randomElement(array_keys($disciplinas));
        $descricao = $disciplinas[$nome];

        return [
            'nome' => $nome,
            'descricao' => $descricao,
            'carga_horaria' => $this->faker->randomElement(['60h', '80h', '120h']),
            'status' => $this->faker->randomElement(['pendente', 'aprovado']),
            'id_usuario_criador' => Usuario::inRandomOrder()->first()->id_usuario ?? null,
        ];
    }
}