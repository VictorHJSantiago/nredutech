<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     *
     * @return void
     */
    public function run(): void
    {
        $adminEmail = 'victorhenriquedejesussantiago@gmail.com';
        $strongPassword = '$argon2id$v=19$m=131072,t=8,p=1$VFZqQUpocS5Pa0FYNmcvRQ$ODJ+ZcAMQsp8IPFSMYhToaWDTzZsvqVV6wflOIlBaMA';

        $user = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin Principal',
                'password' => $strongPassword,
                'email_verified_at' => now(),
            ]
        );

        Usuario::updateOrCreate(
            ['email' => $adminEmail],
            [
                'nome_completo' => 'Victor Henrique de Jesus Santiago',
                'username' => 'admin',
                'password' => $user->password, 
                'status_aprovacao' => 'ativo',
                'tipo_usuario' => 'administrador',
                'data_registro' => now(),
                'email_verified_at' => now(),
                'data_nascimento' => '1990-01-15',
                'cpf' => '123.456.789-00',
                'rg' => '12.345.678-9',
                'rco_siape' => 'SIAPE1234567',
                'telefone' => '(42) 99999-8888',
                'formacao' => 'Doutorado em Administração de Sistemas',
                'area_formacao' => 'Tecnologia da Informação',
                'id_escola' => null,
            ]
        );
    }
}