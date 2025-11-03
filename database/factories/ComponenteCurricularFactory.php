<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ComponenteCurricular;
use App\Models\Usuario;
use App\Models\Escola; 

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
            'Ciências Naturais' => 'Introdução à Biologia, Física e Química.', 
            'Física' => 'Estudo dos fenômenos naturais e das propriedades da matéria.',
            'Química' => 'Estudo da composição e propriedades da matéria.',
            'Biologia' => 'Estudo dos seres vivos e seus processos vitais.',
            'Artes Visuais' => 'Exploração de formas de expressão visual e plástica.', 
            'Filosofia' => 'Discussão sobre questões fundamentais da existência.',
            'Sociologia' => 'Estudo das estruturas e relações sociais.',
            'Língua Inglesa' => 'Aprendizado da língua inglesa.', 
            'Educação Física' => 'Prática de atividades físicas e estudo do corpo humano.', 
            'Ensino Religioso' => 'Estudo das diferentes manifestações religiosas.', 
        ];

        $baseNome = $this->faker->randomElement(array_keys($disciplinas));
        $descricao = $disciplinas[$baseNome];
        $nome = $baseNome . ' ' . $this->faker->unique()->randomNumber(4); 

        $criador = Usuario::inRandomOrder()->first();
        $escolaId = null;
        $status = 'aprovado'; 

        if ($criador) {
            if ($criador->tipo_usuario !== 'administrador') {
                $escolaId = $criador->id_escola;
                $status = 'pendente'; 
            } else {
                $escolaId = null;
                $status = 'aprovado';
            }
        } else {
             $escolaId = null;
             $status = 'aprovado';
        }

        return [
            'nome' => $nome,
            'descricao' => $descricao,
            'carga_horaria' => $this->faker->randomElement([30, 40, 60, 80]), 
            'status' => $status,
            'id_usuario_criador' => $criador?->id_usuario, 
            'id_escola' => $escolaId, 
        ];
    }
}