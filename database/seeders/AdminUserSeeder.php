<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\User; // Importar o model User padrão
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = 'victorhenriquedejesussantiago@gmail.com';
        $adminPassword = '$argon2id$v=19$m=65536,t=4,p=1$UVc3SGJXRWJlOTZGWWlOZw$ZQwxxIuToBDZvsKrfaJgJ4kNwGYJyYVRMTETAaC7fGs'; 
        $user = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin Principal',
                'password' => $adminPassword,
            ]
        );

        Usuario::firstOrCreate(
            ['email' => $adminEmail], 
            [
                'nome_completo' => 'Admin Principal',
                'username' => 'admin',
                'password' => $user->password, 
                'status_aprovacao' => 'ativo',
                'tipo_usuario' => 'administrador',
                'data_registro' => now(),
            ]
        );
    }
}
