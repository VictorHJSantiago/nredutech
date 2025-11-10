<?php

namespace Tests\Feature\Settings;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\UsuarioPreferencia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = Usuario::factory()->create();
    }

    public function test_user_can_save_preferences_when_they_do_not_exist()
    {
        $this->assertDatabaseMissing('usuario_preferencias', [
            'id_usuario' => $this->user->id_usuario,
        ]);

        $data = [
            'notif_email' => true,
            'notif_app' => true,
            'backup_frequency' => 'daily',
        ];

        $response = $this->actingAs($this->user)->post(route('settings.preferences.save'), $data);

        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('success', 'Preferências salvas com sucesso!');
        $this->assertDatabaseHas('usuario_preferencias', [
            'id_usuario' => $this->user->id_usuario,
            'notif_email' => true,
            'notif_app' => true,
            'backup_frequency' => 'daily',
        ]);
    }

    public function test_user_can_update_existing_preferences()
    {
        UsuarioPreferencia::factory()->create([
            'id_usuario' => $this->user->id_usuario,
            'notif_email' => true,
            'notif_app' => true,
            'backup_frequency' => 'daily',
        ]);

        $data = [
            'notif_email' => false,
            'notif_app' => true,
            'backup_frequency' => 'weekly',
        ];

        $response = $this->actingAs($this->user)->post(route('settings.preferences.save'), $data);

        $response->assertRedirect(route('settings'));
        $this->assertDatabaseHas('usuario_preferencias', [
            'id_usuario' => $this->user->id_usuario,
            'notif_email' => false,
            'notif_app' => true,
            'backup_frequency' => 'weekly',
        ]);
    }

    public function test_missing_boolean_preferences_are_saved_as_false()
    {
        $data = [
            'backup_frequency' => 'monthly',
        ];

        $response = $this->actingAs($this->user)->post(route('settings.preferences.save'), $data);

        $response->assertRedirect(route('settings'));
        $this->assertDatabaseHas('usuario_preferencias', [
            'id_usuario' => $this->user->id_usuario,
            'notif_email' => false,
            'notif_app' => false,
            'backup_frequency' => 'monthly',
        ]);
    }

    public function test_save_preferences_fails_validation_with_invalid_data()
    {
        $data = [
            'backup_frequency' => 'anual',
        ];

        $response = $this->actingAs($this->user)->post(route('settings.preferences.save'), $data);

        $response->assertSessionHasErrors('backup_frequency');
        $this->assertDatabaseMissing('usuario_preferencias', [
            'id_usuario' => $this->user->id_usuario,
        ]);
    }
}