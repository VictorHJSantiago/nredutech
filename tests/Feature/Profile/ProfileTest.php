<?php

namespace Tests\Feature\Profile;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('usuarios') && !Schema::hasColumn('usuarios', 'email_verified_at')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->timestamp('email_verified_at')->nullable();
            });
        }
    }

    public function test_pagina_de_perfil_e_exibida()
    {
        $user = Usuario::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
        $response->assertViewIs('profile.edit');
    }

    public function test_informacoes_do_perfil_podem_ser_atualizadas()
    {
        $user = Usuario::factory()->create();

        $response = $this->actingAs($user)
            ->patch('/profile', [
                'nome_completo' => 'Novo Nome',
                'username' => 'novousername',
                'email' => 'novo@email.com',
                'telefone' => '(99) 99999-9999',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Novo Nome', $user->nome_completo);
        $this->assertSame('novo@email.com', $user->email);
        $this->assertSame('novousername', $user->username);
    }

    public function test_senha_pode_ser_atualizada()
    {
        $user = Usuario::factory()->create();

        $response = $this->actingAs($user)
            ->from('/profile')
            ->put(route('password.update'), [
                'current_password' => 'ValidPassword@123456',
                'password' => 'NovaSenhaForte!123',
                'password_confirmation' => 'NovaSenhaForte!123',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $this->assertTrue(Hash::check('NovaSenhaForte!123', $user->refresh()->password));
    }

    public function test_atualizacao_de_senha_falha_com_senha_atual_incorreta()
    {
        $user = Usuario::factory()->create();

        $response = $this->actingAs($user)
            ->from('/profile')
            ->put(route('password.update'), [
                'current_password' => 'senha-errada',
                'password' => 'NovaSenhaForte!123',
                'password_confirmation' => 'NovaSenhaForte!123',
            ]);

        $response->assertSessionHasErrorsIn('updatePassword', 'current_password');
    }
}