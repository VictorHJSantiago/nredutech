<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RecursoDidatico;
use App\Models\Usuario; 
use App\Models\Escola;  

class RecursoDidaticoFactory extends Factory
{
    protected $model = RecursoDidatico::class;

    public function definition(): array
    {
        $recursos = [
            'Projetor Multimídia' => ['Epson', 'Inclui cabo HDMI de 5m e controle remoto. Lâmpada com 500 horas de uso.'],
            'Kit de Robótica Educacional' => ['LEGO Mindstorms', 'Modelo EV3. Contém 541 peças, 3 servomotores e 5 sensores.'],
            'Microscópio Óptico Binocular' => ['Olympus', 'Ampliação de até 1000x. Acompanha conjunto de lâminas preparadas.'],
            'Esqueleto Humano (modelo didático)' => ['Nacional Ossos', 'Tamanho real (1.70m), montado em suporte com rodas. Material: PVC.'],
            'Globo Terrestre Político Interativo' => ['Tecnodidattica', '30cm de diâmetro com iluminação interna (bivolt).'],
            'Kit de Química Orgânica' => ['QNS', 'Conjunto para montagem de moléculas. Contém 116 peças.'],
            'Lousa Digital Interativa' => ['Smart Board', 'Tela de 75 polegadas sensível ao toque. Requer projetor (não incluso).'],
            'Tablet Educacional' => ['Positivo', 'Modelo Tab Q10, 32GB, Android 11. Capa protetora inclusa.'],
            'Conjunto de Mapas Históricos' => ['Editora Moderna', 'Coleção Brasil Colônia e Império, 10 mapas laminados.'],
            'Kit de Primeiros Socorros Escolar' => ['Paramédico', 'Completo com itens básicos para atendimento emergencial.'],
        ];
        $nome = $this->faker->randomElement(array_keys($recursos));
        $detalhes = $recursos[$nome];

        $criador = Usuario::inRandomOrder()->first();
        $escolaId = null;
        $status = 'funcionando';

        if ($criador) {
            if ($criador->tipo_usuario !== 'administrador') {
                $escolaId = $criador->id_escola;
                $status = $this->faker->randomElement(['funcionando', 'funcionando', 'em_manutencao', 'quebrado']);
            } else {
                $escolaId = null;
                $status = 'funcionando';
            }
        }

        return [
            'nome' => $nome . ' ' . $this->faker->unique()->randomNumber(4), 
            'tipo' => $this->faker->randomElement(['didatico', 'laboratorio']),
            'marca' => $detalhes[0],
            'numero_serie' => $this->faker->optional()->ean13(), 
            'quantidade' => $this->faker->numberBetween(1, 5), 
            'observacoes' => $detalhes[1],
            'data_aquisicao' => $this->faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
            'status' => $status, 
            'id_usuario_criador' => $criador?->id_usuario, 
            'id_escola' => $escolaId, 
        ];
    }
}