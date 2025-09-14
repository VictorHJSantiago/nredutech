<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RecursoDidatico;

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
        ];
        $nome = $this->faker->randomElement(array_keys($recursos));
        $detalhes = $recursos[$nome];

        return [
            'nome' => $nome,
            'tipo' => $this->faker->randomElement(['didatico', 'laboratorio']),
            'marca' => $detalhes[0],
            'numero_serie' => $this->faker->unique()->ean13(),
            'quantidade' => $this->faker->numberBetween(1, 10),
            'observacoes' => $detalhes[1],
            'data_aquisicao' => $this->faker->date(),
            'status' => $this->faker->randomElement(['funcionando', 'em_manutencao', 'quebrado']),
        ];
    }
}