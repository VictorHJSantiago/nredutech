<?php

namespace Tests\Feature\User;

use App\Models\Notificacao;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->user = Usuario::factory()->create([
            'status_aprovacao' => 'ativo'
        ]);
    }

    public function test_usuario_pode_ver_pagina_de_notificacoes()
    {
        $response = $this->actingAs($this->user)->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertViewIs('notifications.index');
    }

    public function test_pagina_de_notificacoes_marca_todas_como_lidas_ao_acessar()
    {
        $notificacao = Notificacao::create([
            'titulo' => 'Teste',
            'mensagem' => 'Msg',
            'data_envio' => now(),
            'status_mensagem' => 'enviada',
            'id_usuario' => $this->user->id_usuario
        ]);

        $this->actingAs($this->user)->get(route('notifications.index'));

        $this->assertDatabaseHas('notificacoes', [
            'id_notificacao' => $notificacao->id_notificacao,
            'status_mensagem' => 'lida'
        ]);
    }

    public function test_usuario_pode_excluir_uma_notificacao()
    {
        $notificacao = Notificacao::create([
            'titulo' => 'Teste',
            'mensagem' => 'Msg',
            'data_envio' => now(),
            'status_mensagem' => 'enviada',
            'id_usuario' => $this->user->id_usuario
        ]);

        $response = $this->actingAs($this->user)->delete(route('notifications.destroy', $notificacao->id_notificacao));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('notificacoes', ['id_notificacao' => $notificacao->id_notificacao]);
    }

    public function test_usuario_nao_pode_excluir_notificacao_de_outro()
    {
        $outroUsuario = Usuario::factory()->create();
        $notificacao = Notificacao::create([
            'titulo' => 'Outra',
            'mensagem' => 'Msg',
            'data_envio' => now(),
            'status_mensagem' => 'enviada',
            'id_usuario' => $outroUsuario->id_usuario
        ]);

        $response = $this->actingAs($this->user)->delete(route('notifications.destroy', $notificacao->id_notificacao));
        
        if ($response->status() === 404) {
             $this->assertTrue(true);
        } elseif ($response->status() === 302) {
             $this->assertTrue(true);
        } else {
             $response->assertForbidden();
        }
        
        $this->assertDatabaseHas('notificacoes', ['id_notificacao' => $notificacao->id_notificacao]);
    }

    public function test_usuario_pode_limpar_todas_notificacoes()
    {
        Notificacao::create([
            'titulo' => 'Teste 1',
            'mensagem' => 'Msg',
            'data_envio' => now(),
            'status_mensagem' => 'enviada',
            'id_usuario' => $this->user->id_usuario
        ]);

        $response = $this->actingAs($this->user)->post(route('notifications.clearAll'));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('notificacoes', ['id_usuario' => $this->user->id_usuario]);
    }
}