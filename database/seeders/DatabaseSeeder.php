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
        // Chama os seeders iniciais para o admin e as escolas
        $this->call([
            AdminUserSeeder::class,
            NreIratiSeeder::class,
        ]);

        $statusOptions = ['ativo', 'pendente', 'bloqueado'];

        // 1. Cria 5 Administradores com status variado e 2 extras como bloqueados
        for ($i = 0; $i < 5; $i++) {
            Usuario::factory()->create([
                'tipo_usuario' => 'administrador',
                'id_escola' => null,
                'status_aprovacao' => $statusOptions[array_rand(['ativo', 'pendente'])], // Varia entre ativo e pendente
            ]);
        }
        Usuario::factory()->count(2)->create([
            'tipo_usuario' => 'administrador',
            'id_escola' => null,
            'status_aprovacao' => 'bloqueado', // Excedem o limite
        ]);


        // 2. Itera sobre cada escola para popular com diretores e professores
        $escolas = Escola::all();
        foreach ($escolas as $escola) {
            // Cria 2 Diretores com status variado e 1 extra como bloqueado
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

            // Cria 3 Professores com status variado e 1 extra como bloqueado
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

        // 3. Cria recursos e componentes de forma genérica
        RecursoDidatico::factory(200)->create();
        ComponenteCurricular::factory(50)->create();
    }
}