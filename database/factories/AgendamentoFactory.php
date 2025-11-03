<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\Agendamento;
use App\Models\OfertaComponente;
use App\Models\RecursoDidatico; 

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agendamento>
 */
class AgendamentoFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Agendamento::class; 

    /**
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $oferta = OfertaComponente::with('turma.escola')->whereHas('turma.escola')->inRandomOrder()->first();
        if (!$oferta || !$oferta->turma || !$oferta->turma->id_escola) {
            throw new \Exception('Nenhuma oferta com turma e escola válidas encontrada para criar o agendamento.');
        }
        $escolaId = $oferta->turma->id_escola;
        $recurso = RecursoDidatico::where('status', 'funcionando')
                                     ->where(function ($query) use ($escolaId) {
                                         $query->whereNull('id_escola')
                                               ->orWhere('id_escola', $escolaId);
                                     })
                                     ->inRandomOrder()
                                     ->first();
        if (!$recurso) {
            throw new \Exception("Nenhum recurso didático funcionando (global ou da escola ID {$escolaId}) encontrado para criar o agendamento.");
        }

        $startDate = Carbon::instance($this->faker->dateTimeBetween('now', '+1 month'));
        if ($startDate->hour < 6) $startDate->hour(6)->minute(0);
        if ($startDate->hour >= 22) $startDate->hour(21)->minute(0);

        $endDate = $startDate->copy()->addHours($this->faker->numberBetween(1, 2));

        return [
            'data_hora_inicio' => $startDate,
            'data_hora_fim' => $endDate,
            'status' => 'agendado', 
            'id_oferta' => $oferta->id_oferta,
            'id_recurso' => $recurso->id_recurso,
        ];
    }
}