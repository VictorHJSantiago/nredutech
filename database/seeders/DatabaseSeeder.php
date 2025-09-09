<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
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

        Usuario::factory(1000)->create();
        RecursoDidatico::factory(100000)->create();
        ComponenteCurricular::factory(1000)->create();
    }
}
