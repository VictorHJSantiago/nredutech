<?php

namespace Tests\Feature\Settings;

use App\Models\Usuario;
use App\Models\UsuarioPreferencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
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
        
        UsuarioPreferencia::create([
            'id_usuario' => $this->user->id_usuario,
            'notif_email' => true,
            'notif_popup' => true,
            'tema' => 'claro',
            'tamanho_fonte' => 'padrao'
        ]);
    }

    public function test_atualizacao_de_preferencias_salva_alteracoes_no_banco()
    {
        $dados = [
            'notif_email' => false,
            'notif_popup' => false, 
            'tema' => 'escuro',
            'tamanho_fonte' => 'grande'
        ];

        $response = $this->actingAs($this->user)
            ->patch(route('settings.preferences.update'), $dados);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('usuario_preferencias', [
            'id_usuario' => $this->user->id_usuario,
            'notif_email' => 0,
            'tema' => 'escuro',
            'tamanho_fonte' => 'grande'
        ]);
    }

    public function test_novo_usuario_consegue_atualizar_preferencias()
    {
        $novoUsuario = Usuario::factory()->create(['status_aprovacao' => 'ativo']);
        
        UsuarioPreferencia::create([
            'id_usuario' => $novoUsuario->id_usuario,
            'tema' => 'claro',
            'notif_email' => true,
            'notif_popup' => true,
            'tamanho_fonte' => 'padrao'
        ]);

        $dados = [
            'tema' => 'escuro',
            'notif_email' => true,
            'notif_popup' => true,
            'tamanho_fonte' => 'padrao'
        ];

        $response = $this->actingAs($novoUsuario)
            ->patch(route('settings.preferences.update'), $dados);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('usuario_preferencias', [
            'id_usuario' => $novoUsuario->id_usuario,
            'tema' => 'escuro'
        ]);
    }

    public function test_atualizacao_de_preferencias_retorna_erro_com_dados_invalidos()
    {
        $dados = [
            'tema' => 'tema-inexistente',
            'tamanho_fonte' => 'extra-gigante'
        ];

        $response = $this->actingAs($this->user)
            ->patch(route('settings.preferences.update'), $dados);

        $response->assertSessionHasErrors(['tema', 'tamanho_fonte']);
    }

    public function test_admin_consegue_atualizar_agendamento_de_backup()
    {
        if (!\Illuminate\Support\Facades\Route::has('settings.backup.schedule.update')) {
            $this->markTestSkipped('Rota de agendamento de backup não encontrada.');
        }

        $admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'status_aprovacao' => 'ativo']);
        
        UsuarioPreferencia::create([
            'id_usuario' => $admin->id_usuario,
            'tema' => 'claro',
            'notif_email' => true,
            'notif_popup' => true,
            'tamanho_fonte' => 'padrao'
        ]);

        $response = $this->actingAs($admin)->patch(route('settings.backup.schedule.update'), [
            'backup_frequency' => 'daily'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_tentativa_de_download_de_backup_sem_arquivo_retorna_resposta_valida()
    {
        if (!\Illuminate\Support\Facades\Route::has('settings.backup.download.latest')) {
            $this->markTestSkipped('Rota de download de backup não encontrada.');
        }

        $admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'status_aprovacao' => 'ativo']);

        $response = $this->actingAs($admin)->get(route('settings.backup.download.latest'));

        $response->assertStatus(200); 
    }
}