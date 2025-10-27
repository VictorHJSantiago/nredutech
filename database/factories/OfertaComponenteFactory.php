<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Turma;
use App\Models\Usuario;
use App\Models\ComponenteCurricular;
use App\Models\OfertaComponente; 

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfertaComponente>
 */
class OfertaComponenteFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = OfertaComponente::class; 

    /**
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $turma = Turma::whereNotNull('id_escola')->inRandomOrder()->first();
        if (!$turma) {
            throw new \Exception('Nenhuma turma com escola associada encontrada para criar a oferta.');
        }
        $escolaId = $turma->id_escola;
        $professor = Usuario::where('tipo_usuario', 'professor')
                           ->where('id_escola', $escolaId)
                           ->where('status_aprovacao', 'ativo')
                           ->inRandomOrder()
                           ->first();
        if (!$professor) {
             $professor = Usuario::where('tipo_usuario', 'diretor')
                           ->where('id_escola', $escolaId)
                           ->where('status_aprovacao', 'ativo')
                           ->inRandomOrder()
                           ->first();
        }

        if (!$professor) {
             $professor = Usuario::where('tipo_usuario', 'administrador')
                           ->where('status_aprovacao', 'ativo')
                           ->inRandomOrder()
                           ->first();
        }

         if (!$professor) {
            throw new \Exception("Nenhum professor, diretor ou administrador ativo encontrado (Escola: {$escolaId}) para criar a oferta.");
        }
        $componente = ComponenteCurricular::where('status', 'aprovado')
                                        ->where(function ($query) use ($escolaId) {
                                            $query->whereNull('id_escola')
                                                  ->orWhere('id_escola', $escolaId);
                                        })
                                        ->inRandomOrder()
                                        ->first();
        if (!$componente) {
            throw new \Exception("Nenhum componente curricular aprovado (global ou da escola ID {$escolaId}) encontrado para criar a oferta.");
        }

        return [
            'id_turma' => $turma->id_turma,
            'id_professor' => $professor->id_usuario,
            'id_componente' => $componente->id_componente,
        ];
    }
}