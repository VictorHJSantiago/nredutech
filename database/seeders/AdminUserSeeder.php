<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = 'victorhenriquedejesussantiago@gmail.com';
        $adminPassword = '$argon2id$v=19$m=131072,t=8,p=1$VFlPUXhwQ3d4eXBXQUh2TQ$xMQ6GP9XLaAaa3QN+3FWeqGFujGZ83IAWOgRU9EOjqg'; 
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
