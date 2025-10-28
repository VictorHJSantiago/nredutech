<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Notificacao;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $usuario;

    protected function setUp(): void
    {
        parent::setUp();
        $this->usuario = Usuario::factory()->create();
    }

    /** @test */
    public function index_marca_notificacoes_enviadas_como_lidas()
    {
        Notificacao::factory()->create([
            'id_usuario' => $this->usuario->id_usuario,
            'status_mensagem' => 'enviada'
        ]);

        $this->assertDatabaseHas('notificacoes', ['id_usuario' => $this->usuario->id_usuario, 'status_mensagem' => 'enviada']);

        $response = $this->actingAs($this->usuario)->get(route('notifications.index'));

        $response->assertStatus(200);
        $this->assertDatabaseHas('notificacoes', ['id_usuario' => $this->usuario->id_usuario, 'status_mensagem' => 'lida']);
    }

    /** @test */
    public function usuario_pode_limpar_todas_suas_notificacoes()
    {
        Notificacao::factory(3)->create(['id_usuario' => $this->usuario->id_usuario]);
        $this->assertDatabaseCount('notificacoes', 3);

        $response = $this->actingAs($this->usuario)->post(route('notifications.clearAll'));

        $response->assertRedirect(route('notifications.index'));
        $this->assertDatabaseCount('notificacoes', 0);
    }

    /** @test */
    public function usuario_nao_pode_limpar_notificacoes_de_outro_usuario()
    {
        $outroUsuario = Usuario::factory()->create();
        Notificacao::factory(3)->create(['id_usuario' => $this->usuario->id_usuario]);
        Notificacao::factory(2)->create(['id_usuario' => $outroUsuario->id_usuario]);
        $this->assertDatabaseCount('notificacoes', 5);
        $this->actingAs($this->usuario)->post(route('notifications.clearAll'));
        $this->assertDatabaseCount('notificacoes', 2);
        $this->assertDatabaseMissing('notificacoes', ['id_usuario' => $this->usuario->id_usuario]);
        $this->assertDatabaseHas('notificacoes', ['id_usuario' => $outroUsuario->id_usuario]);
    }
}