<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario; 
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::firstOrCreate(
            [
                'email' => ''
            ],
            [
                'nome_completo' => 'Admin Principal',
                'username' => 'admin',
                'password' => Hash::make(''), 
                'status_aprovacao' => 'ativo',
                'tipo_usuario' => 'administrador',
                'data_registro' => now(),
            ]
        );
    }
}