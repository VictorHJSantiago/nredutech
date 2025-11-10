<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Notificacao;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $user;
    private Notificacao $notificacaoLida;
    private Notificacao $notificacaoNaoLida;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = Usuario::factory()->create();
        
        $this->notificacaoLida = Notificacao::factory()->create([
            'id_usuario' => $this->user->id_usuario,
            'status_leitura' => 'lida',
        ]);

        $this->notificacaoNaoLida = Notificacao::factory()->create([
            'id_usuario' => $this->user->id_usuario,
            'status_leitura' => 'nao_lida',
        ]);
    }

    public function test_user_can_view_notifications_page()
    {
        $response = $this->actingAs($this->user)->get(route('notifications.index'));

        $response->assertOk();
        $response->assertViewIs('notifications.index');
        $response->assertViewHas('notificacoes');
        $response->assertSee($this->notificacaoLida->titulo);
        $response->assertSee($this->notificacaoNaoLida->titulo);
    }

    public function test_notifications_page_marks_all_as_read()
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

    public function test_user_can_delete_a_notification()
    {
        $response = $this->actingAs($this->user)->delete(route('notifications.destroy', $this->notificacaoLida));

        $response->assertRedirect(route('notifications.index'));
        $response->assertSessionHas('success', 'Notificação excluída com sucesso.');
        $this->assertDatabaseMissing('notificacoes', [
            'id_notificacao' => $this->notificacaoLida->id_notificacao,
        ]);
    }

    public function test_user_cannot_delete_another_users_notification()
    {
        $outroUsuario = Usuario::factory()->create();
        $outraNotificacao = Notificacao::factory()->create(['id_usuario' => $outroUsuario->id_usuario]);

        $response = $this->actingAs($this->user)->delete(route('notifications.destroy', $outraNotificacao));

        $response->assertForbidden();
        $this->assertDatabaseHas('notificacoes', [
            'id_notificacao' => $outraNotificacao->id_notificacao,
        ]);
    }

    public function test_user_can_clear_all_notifications()
    {
        $response = $this->actingAs($this->user)->post(route('notifications.clearAll'));

        $response->assertRedirect(route('notifications.index'));
        $response->assertSessionHas('success', 'Todas as notificações foram excluídas.');
        $this->assertDatabaseMissing('notificacoes', [
            'id_usuario' => $this->user->id_usuario,
        ]);
    }
}