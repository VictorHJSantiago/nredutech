
<?php
/*****
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\RecursoDidatico;
use App\Models\ComponenteCurricular;
use App\Models\Turma;
use App\Models\OfertaComponente;
use App\Models\Agendamento;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            NreIratiSeeder::class,
        ]);

        Usuario::factory(4)->create(['tipo_usuario' => 'administrador', 'id_escola' => null]);
        Escola::all()->each(function ($escola) {
            Usuario::factory(2)->create([
                'tipo_usuario' => 'diretor',
                'id_escola' => $escola->id_escola,
            ]);
            $professores = Usuario::factory(3)->create([
                'tipo_usuario' => 'professor',
                'id_escola' => $escola->id_escola,
            ]);
            $turmas = Turma::factory(5)->create([
                'id_escola' => $escola->id_escola,
            ]);
            $turmas->each(function ($turma) use ($professores) {
                $disciplinas = ComponenteCurricular::factory(5)->create([
                    'id_usuario_criador' => Usuario::inRandomOrder()->first()->id_usuario
                ]);
                
                $disciplinas->each(function ($disciplina) use ($turma, $professores) {
                    OfertaComponente::factory()->create([
                        'id_turma' => $turma->id_turma,
                        'id_componente' => $disciplina->id_componente,
                        'id_professor' => $professores->random()->id_usuario,
                    ]);
                });
            });
        });

        RecursoDidatico::factory(50)->create();
        $ofertas = OfertaComponente::all();
        $recursos = RecursoDidatico::where('status', 'funcionando')->get();

        if ($ofertas->isNotEmpty() && $recursos->isNotEmpty()) {
            for ($i = 0; $i < 100; $i++) {
                Agendamento::factory()->create([
                    'id_oferta' => $ofertas->random()->id_oferta,
                    'id_recurso' => $recursos->random()->id_recurso,
                ]);
            }
        }
    }
}




<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\RecursoDidatico;
use App\Models\ComponenteCurricular;
use App\Models\Turma;
use App\Models\OfertaComponente;
use App\Models\Agendamento;

class DatabaseSeeder extends Seeder
{
    /**
     *
     * @return void
     
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            NreSeeder::class,
        ]);

        Usuario::factory(4)->create(['tipo_usuario' => 'administrador', 'id_escola' => null]);
        Escola::all()->each(function ($escola) {
            
            Usuario::factory(2)->create([
                'tipo_usuario' => 'diretor',
                'id_escola' => $escola->id_escola,
            ]);

            $professores = Usuario::factory(3)->create([
                'tipo_usuario' => 'professor',
                'id_escola' => $escola->id_escola,
            ]);

            $turmas = Turma::factory(5)->create([
                'id_escola' => $escola->id_escola,
            ]);

            $turmas->each(function ($turma) use ($professores) {
                $disciplinas = ComponenteCurricular::factory(5)->create([
                    'id_usuario_criador' => Usuario::inRandomOrder()->first()->id_usuario
                ]);
                
                $disciplinas->each(function ($disciplina) use ($turma, $professores) {
                    OfertaComponente::factory()->create([
                        'id_turma' => $turma->id_turma,
                        'id_componente' => $disciplina->id_componente,
                        'id_professor' => $professores->random()->id_usuario,
                    ]);
                });
            });
        });
        RecursoDidatico::factory(100000)->create();
        $ofertas = OfertaComponente::all();
        $recursos = RecursoDidatico::where('status', 'funcionando')->get();

        if ($ofertas->isNotEmpty() && $recursos->isNotEmpty()) {
            for ($i = 0; $i < 100; $i++) {
                Agendamento::factory()->create([
                    'id_oferta' => $ofertas->random()->id_oferta,
                    'id_recurso' => $recursos->random()->id_recurso,
                ]);
            }
        }
    }
}
****
*/
