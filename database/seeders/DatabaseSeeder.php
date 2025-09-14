<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\RecursoDidatico;
use App\Models\ComponenteCurricular;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            NreIratiSeeder::class,
        ]);

        $statusOptions = ['ativo', 'pendente', 'bloqueado'];

        for ($i = 0; $i < 5; $i++) {
            Usuario::factory()->create([
                'tipo_usuario' => 'administrador',
                'id_escola' => null,
                'status_aprovacao' => $statusOptions[array_rand(['ativo', 'pendente'])], 
            ]);
        }
        Usuario::factory()->count(2)->create([
            'tipo_usuario' => 'administrador',
            'id_escola' => null,
            'status_aprovacao' => 'bloqueado', 
        ]);


        $escolas = Escola::all();
        foreach ($escolas as $escola) {
            for ($i = 0; $i < 2; $i++) {
                Usuario::factory()->create([
                    'tipo_usuario' => 'diretor',
                    'id_escola' => $escola->id_escola,
                    'status_aprovacao' => $statusOptions[array_rand(['ativo', 'pendente'])],
                ]);
            }
            Usuario::factory()->create([
                'tipo_usuario' => 'diretor',
                'id_escola' => $escola->id_escola,
                'status_aprovacao' => 'bloqueado',
            ]);

            for ($i = 0; $i < 3; $i++) {
                Usuario::factory()->create([
                    'tipo_usuario' => 'professor',
                    'id_escola' => $escola->id_escola,
                    'status_aprovacao' => $statusOptions[array_rand(['ativo', 'pendente'])],
                ]);
            }
            Usuario::factory()->create([
                'tipo_usuario' => 'professor',
                'id_escola' => $escola->id_escola,
                'status_aprovacao' => 'bloqueado',
            ]);
        }

        RecursoDidatico::factory(200)->create();
        ComponenteCurricular::factory(50)->create();
    }
}