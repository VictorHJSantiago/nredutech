<?php

namespace Tests\Feature\Settings;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingsRoutesTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretor = Usuario::factory()->diretor()->create();
        $this->professor = Usuario::factory()->professor()->create();
    }

    public function test_guest_is_redirected_from_all_settings_routes()
    {
        $this->get(route('settings'))->assertRedirect(route('login'));
        $this->post(route('settings.preferences.save'))->assertRedirect(route('login'));
        $this->post(route('settings.backup.run'))->assertRedirect(route('login'));
        $this->get(route('settings.backup.files'))->assertRedirect(route('login'));
        $this->get(route('settings.backup.download', ['fileName' => 'test.zip']))->assertRedirect(route('login'));
        $this->delete(route('settings.backup.delete', ['fileName' => 'test.zip']))->assertRedirect(route('login'));
        $this->get(route('settings.restore'))->assertRedirect(route('login'));
        $this->post(route('settings.restore.upload'))->assertRedirect(route('login'));
    }

    public function test_professor_can_access_own_settings_routes_only()
    {
        $this->actingAs($this->professor);

        $this->get(route('settings'))->assertOk();
        $this->post(route('settings.preferences.save'), [])->assertRedirect();

        $this->post(route('settings.backup.run'))->assertForbidden();
        $this->get(route('settings.backup.files'))->assertForbidden();
        $this->get(route('settings.backup.download', ['fileName' => 'test.zip']))->assertForbidden();
        $this->delete(route('settings.backup.delete', ['fileName' => 'test.zip']))->assertForbidden();
        $this->get(route('settings.restore'))->assertForbidden();
        $this->post(route('settings.restore.upload'))->assertForbidden();
    }

    public function test_diretor_can_access_own_settings_routes_only()
    {
        $this->actingAs($this->diretor);

        $this->get(route('settings'))->assertOk();
        $this->post(route('settings.preferences.save'), [])->assertRedirect();

        $this->post(route('settings.backup.run'))->assertForbidden();
        $this->get(route('settings.backup.files'))->assertForbidden();
        $this->get(route('settings.backup.download', ['fileName' => 'test.zip']))->assertForbidden();
        $this->delete(route('settings.backup.delete', ['fileName' => 'test.zip']))->assertForbidden();
        $this->get(route('settings.restore'))->assertForbidden();
        $this->post(route('settings.restore.upload'))->assertForbidden();
    }

    public function test_admin_can_access_all_settings_routes()
    {
        $this->actingAs($this->admin);

        $this->get(route('settings'))->assertOk();
        $this->post(route('settings.preferences.save'), [])->assertRedirect();
        
        $this->post(route('settings.backup.run'))->assertRedirect();
        $this->get(route('settings.backup.files'))->assertOk();
        $this->get(route('settings.backup.download', ['fileName' => 'test.zip']))->assertNotFound();
        $this->delete(route('settings.backup.delete', ['fileName' => 'test.zip']))->assertNotFound();
        $this->get(route('settings.restore'))->assertOk();
        $this->post(route('settings.restore.upload'), [])->assertRedirect();
    }
}