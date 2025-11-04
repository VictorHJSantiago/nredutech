<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::create([
            'nome_completo' => 'Victor Henrique de Jesus Santiago',
            'username' => 'vhjsantiago',
            'email' => 'victorhenriquedejesussantiago@gmail.com',
            'data_nascimento' => '2005-08-25',
            'cpf' => '905.979.410-92',
            'rg' => '12.345.678-9',
            'rco_siape' => '1234567',
            'telefone' => '+5542999999999',
            'formacao' => 'Técnico em Desenvolvimento de Sistemas',
            'area_formacao' => 'Tecnologia da Informação',
            'data_registro' => Carbon::now(),
            'status_aprovacao' => 'ativo',
            'tipo_usuario' => 'administrador',
            'id_escola' => null,
            'password' => '$argon2id$v=19$m=131072,t=8,p=1$VFZqQUpocS5Pa0FYNmcvRQ$ODJ+ZcAMQsp8IPFSMYhToaWDTzZsvqVV6wflOIlBaMA',
        ]);
    }
}