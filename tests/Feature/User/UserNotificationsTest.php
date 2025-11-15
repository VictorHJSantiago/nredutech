<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Notificacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;

class UserNotificationsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Usuario $user;
    private Notificacao $notificacaoLida;
    private Notificacao $notificacaoNaoLida;

    protected function setUp(): void
    {
        parent::setUp();

        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $escola = Escola::create(['nome' => 'Escola Teste', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        
        $this->user = Usuario::factory()->create($this->getValidUserData(['tipo_usuario' => 'professor', 'id_escola' => $escola->id_escola]));
        
        $this->notificacaoLida = Notificacao::create([
            'id_usuario' => $this->user->id_usuario,
            'status_leitura' => 'lida',
            'titulo' => 'Notificacao Lida Teste',
            'mensagem' => 'Mensagem de teste.',
            'data_envio' => now(),
        ]);

        $this->notificacaoNaoLida = Notificacao::create([
            'id_usuario' => $this->user->id_usuario,
            'status_leitura' => 'nao_lida',
            'titulo' => 'Notificacao Nao Lida Teste',
            'mensagem' => 'Mensagem de teste.',
            'data_envio' => now(),
        ]);
    }

    private function getValidUserData(array $overrides = []): array
    {
        return array_merge([
            'cpf' => $this->faker->unique()->cpf(false),
            'password' => 'ValidPassword@123456',
            'data_nascimento' => now()->subYears(20)->format('Y-m-d'),
        ], $overrides);
    }

    #[Test]
    public function usuario_pode_ver_pagina_de_notificacoes()
    {
        $response = $this->actingAs($this->user)->get(route('notifications.index'));

        $response->assertOk();
        $response->assertViewIs('notifications.index');
        $response->assertViewHas('notificacoes');
        $response->assertSee($this->notificacaoLida->titulo);
        $response->assertSee($this->notificacaoNaoLida->titulo);
    }

    #[Test]
    public function pagina_de_notificacoes_marca_todas_como_lidas()
    {
        $this->assertDatabaseHas('notificacoes', [
            'id_notificacao' => $this->notificacaoNaoLida->id_notificacao,
            'status_leitura' => 'nao_lida',
        ]);

        $this->actingAs($this->user)->get(route('notifications.index'));

        $this->assertDatabaseHas('notificacoes', [
            'id_notificacao' => $this->notificacaoNaoLida->id_notificacao,
            'status_leitura' => 'lida',
        ]);
    }

    #[Test]
    public function usuario_pode_excluir_uma_notificacao()
    {
        $response = $this->actingAs($this->user)->delete(route('notifications.destroy', $this->notificacaoLida));

        $response->assertRedirect(route('notifications.index'));
        $response->assertSessionHas('success', 'Notificação removida com sucesso.');
        $this->assertDatabaseMissing('notificacoes', [
            'id_notificacao' => $this->notificacaoLida->id_notificacao,
        ]);
    }

    #[Test]
    public function usuario_nao_pode_excluir_notificacao_de_outro_usuario()
    {
        $outroUsuario = Usuario::factory()->create($this->getValidUserData(['id_escola' => $this->user->id_escola]));
        $outraNotificacao = Notificacao::create([
            'id_usuario' => $outroUsuario->id_usuario,
            'titulo' => 'Outra Notificacao',
            'mensagem' => 'Outra Mensagem.',
            'status_leitura' => 'nao_lida',
            'data_envio' => now(),
        ]);

        $response = $this->actingAs($this->user)->delete(route('notifications.destroy', $outraNotificacao));

        $response->assertForbidden();
        $this->assertDatabaseHas('notificacoes', [
            'id_notificacao' => $outraNotificacao->id_notificacao,
        ]);
    }

    #[Test]
    public function usuario_pode_limpar_todas_notificacoes()
    {
        $response = $this->actingAs($this->user)->post(route('notifications.clearAll'));

        $response->assertRedirect(route('notifications.index'));
        $response->assertSessionHas('success', 'Todas as suas notificações foram limpas.');
        $this->assertDatabaseMissing('notificacoes', [
            'id_usuario' => $this->user->id_usuario,
        ]);
    }
}