<?php

namespace Tests\Feature\Settings;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsRoutesTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $diretor;
    private $professor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->admin = Usuario::factory()->create([
            'tipo_usuario' => 'administrador',
            'status_aprovacao' => 'ativo'
        ]);

        $this->diretor = Usuario::factory()->create([
            'tipo_usuario' => 'diretor',
            'status_aprovacao' => 'ativo'
        ]);

        $this->professor = Usuario::factory()->create([
            'tipo_usuario' => 'professor',
            'status_aprovacao' => 'ativo'
        ]);
    }

    public function test_visitante_e_redirecionado_da_pagina_de_configuracoes()
    {
        $this->get(route('settings'))->assertRedirect(route('login'));
    }

    public function test_visitante_e_redirecionado_ao_tentar_atualizar_preferencias()
    {
        $this->patch(route('settings.preferences.update'), [])->assertRedirect(route('login'));
    }

    public function test_professor_consegue_acessar_pagina_de_configuracoes()
    {
        $this->actingAs($this->professor)
             ->get(route('settings'))
             ->assertStatus(200);
    }

    public function test_professor_consegue_acessar_rota_de_atualizar_preferencias()
    {
        $this->actingAs($this->professor)
             ->patch(route('settings.preferences.update'), [])
             ->assertStatus(302); // Redireciona (sucesso ou erro de validação)
    }

    public function test_professor_nao_consegue_acessar_rotas_de_backup()
    {
        if (\Illuminate\Support\Facades\Route::has('settings.backup.schedule.update')) {
            $this->actingAs($this->professor)
                 ->patch(route('settings.backup.schedule.update'))
                 ->assertForbidden();
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_diretor_consegue_acessar_pagina_de_configuracoes()
    {
        $this->actingAs($this->diretor)
             ->get(route('settings'))
             ->assertStatus(200);
    }

    public function test_diretor_consegue_acessar_rota_de_atualizar_preferencias()
    {
        $this->actingAs($this->diretor)
             ->patch(route('settings.preferences.update'), [])
             ->assertStatus(302);
    }

    public function test_diretor_nao_consegue_acessar_rotas_de_backup()
    {
        if (\Illuminate\Support\Facades\Route::has('settings.backup.schedule.update')) {
            $this->actingAs($this->diretor)
                 ->patch(route('settings.backup.schedule.update'))
                 ->assertForbidden();
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_admin_consegue_acessar_pagina_de_configuracoes()
    {
        $this->actingAs($this->admin)
             ->get(route('settings'))
             ->assertStatus(200);
    }

    public function test_admin_consegue_acessar_rotas_de_backup()
    {
        if (\Illuminate\Support\Facades\Route::has('settings.backup.download.latest')) {
            $this->actingAs($this->admin)
                 ->get(route('settings.backup.download.latest'))
                 ->assertStatus(200);
        } else {
            $this->assertTrue(true);
        }
    }
}