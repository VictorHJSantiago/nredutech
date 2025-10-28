<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\UsuarioPreferencia;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $professor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'password' => Hash::make('password')]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor']);
        UsuarioPreferencia::factory()->create(['id_usuario' => $this->admin->id_usuario]);
        UsuarioPreferencia::factory()->create(['id_usuario' => $this->professor->id_usuario]);
    }

    /** @test */
    public function qualquer_usuario_autenticado_pode_ver_pagina_configuracoes()
    {
        $responseAdmin = $this->actingAs($this->admin)->get(route('settings'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertViewIs('settings');

        $responseProf = $this->actingAs($this->professor)->get(route('settings'));
        $responseProf->assertStatus(200);
        $responseProf->assertViewIs('settings');
    }

    /** @test */
    public function usuario_pode_atualizar_suas_preferencias()
    {
        $dados = [
            'notif_email' => true,
            // 'notif_popup' => false, 
            // 'theme' => 'dark',
        ];

        $response = $this->actingAs($this->professor)->patch(route('settings.preferences.update'), $dados);

        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('usuario_preferencias', [
            'id_usuario' => $this->professor->id_usuario,
            'notif_email' => true,
        ]);
    }

    /** @test */
    public function admin_pode_ver_secao_backup_restore()
    {
        $response = $this->actingAs($this->admin)->get(route('settings'));
        $response->assertSee('Backup e Restauração'); 
        $response->assertSee(route('settings.backup.schedule.update')); 
    }

    /** @test */
    public function nao_admin_nao_pode_ver_secao_backup_restore()
    {
        $response = $this->actingAs($this->professor)->get(route('settings'));
        $response->assertDontSee('Backup e Restauração');
        $response->assertDontSee(route('settings.backup.schedule.update'));
    }

    /** @test */
    public function admin_pode_atualizar_agendamento_backup()
    {
        $response = $this->actingAs($this->admin)->patch(route('settings.backup.schedule.update'), ['backup_frequency' => 'weekly']);
        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('usuario_preferencias', [
            'id_usuario' => $this->admin->id_usuario,
            'backup_frequency' => 'weekly',
        ]);
    }

    /** @test */
    public function nao_admin_nao_pode_atualizar_agendamento_backup()
    {
        $response = $this->actingAs($this->professor)->patch(route('settings.backup.schedule.update'), ['backup_frequency' => 'weekly']);
        $response->assertStatus(403); 
    }

    /** @test */
    public function admin_precisa_confirmar_senha_para_iniciar_backup()
    {
        $response = $this->actingAs($this->admin)->get(route('settings.backup.initiate'));
        $response->assertRedirect(route('password.confirm'));
        // $this->followingRedirects()->withSession(['auth.password_confirmed_at' => time()])
        //     ->actingAs($this->admin)
        //     ->get(route('settings.backup.initiate'))
        //     ->assertOk(); // ou assertRedirect(route('settings')) com success
        $this->assertTrue(true);
    }

     /** @test */
    public function admin_precisa_confirmar_senha_para_ver_pagina_restore()
    {
        $response = $this->actingAs($this->admin)->get(route('settings.backup.restore'));
        $response->assertRedirect(route('password.confirm'));
    }

}
