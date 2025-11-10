<?php

namespace Tests\Feature\Settings;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\UploadedFile;
use Spatie\Backup\Jobs\BackupJob;

class SettingsControllerTest extends TestCase
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

        Storage::fake('local');
        Storage::fake('backup_disk');
        Bus::fake();
        Artisan::spy();
    }

    public function test_admin_sees_municipios_on_settings_index()
    {
        Municipio::factory()->count(3)->create();
        $response = $this->actingAs($this->admin)->get(route('settings'));
        
        $response->assertOk();
        $response->assertViewIs('settings.index');
        $response->assertViewHas('municipios', fn ($municipios) => $municipios->count() === 3);
    }

    public function test_diretor_does_not_see_municipios_on_settings_index()
    {
        $response = $this->actingAs($this->diretor)->get(route('settings'));
        
        $response->assertOk();
        $response->assertViewIs('settings.index');
        $response->assertViewMissing('municipios');
    }

    public function test_professor_does_not_see_municipios_on_settings_index()
    {
        $response = $this->actingAs($this->professor)->get(route('settings'));
        
        $response->assertOk();
        $response->assertViewIs('settings.index');
        $response->assertViewMissing('municipios');
    }

    public function test_admin_can_run_backup()
    {
        $response = $this->actingAs($this->admin)->post(route('settings.backup.run'));
        
        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('success', 'Backup iniciado com sucesso! Você será notificado por e-mail quando terminar.');
        Bus::assertDispatched(BackupJob::class);
    }

    public function test_admin_can_get_backup_files()
    {
        Storage::disk('backup_disk')->put('NREduTech/backup-test-1.zip', 'content');
        Storage::disk('backup_disk')->put('NREduTech/backup-test-2.zip', 'content');

        $response = $this->actingAs($this->admin)->get(route('settings.backup.files'));

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonFragment(['name' => 'backup-test-1.zip']);
    }

    public function test_admin_can_download_backup()
    {
        Storage::disk('backup_disk')->put('NREduTech/backup-test-1.zip', 'dummy content');
        
        $response = $this->actingAs($this->admin)->get(route('settings.backup.download', ['fileName' => 'backup-test-1.zip']));

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename=backup-test-1.zip');
    }

    public function test_admin_can_delete_backup()
    {
        Storage::disk('backup_disk')->put('NREduTech/backup-test-1.zip', 'dummy content');
        Storage::disk('backup_disk')->assertExists('NREduTech/backup-test-1.zip');
        
        $response = $this->actingAs($this->admin)->delete(route('settings.backup.delete', ['fileName' => 'backup-test-1.zip']));

        $response->assertOk();
        $response->assertJson(['success' => true]);
        Storage::disk('backup_disk')->assertMissing('NREduTech/backup-test-1.zip');
    }

    public function test_admin_can_view_restore_page()
    {
        $response = $this->actingAs($this->admin)->get(route('settings.restore'));
        $response->assertOk();
        $response->assertViewIs('settings.restore');
    }

    public function test_admin_can_upload_and_restore_backup()
    {
        $file = UploadedFile::fake()->create('backup.zip', 100, 'application/zip');

        $response = $this->actingAs($this->admin)->post(route('settings.restore.upload'), [
            'backup_file' => $file,
        ]);
        
        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('success', 'Restauração iniciada com sucesso! O sistema está sendo restaurado.');

        Storage::disk('local')->assertExists('temp_restores/' . $file->hashName());
        Artisan::shouldHaveReceived('call')->with('backup:restore', [
            '--backup' => 'local://temp_restores/' . $file->hashName(),
            '--source' => 'local',
            '--yes' => true,
        ]);
        
        Storage::disk('local')->assertMissing('temp_restores/' . $file->hashName());
    }

    public function test_upload_restore_fails_validation_without_file()
    {
        $response = $this->actingAs($this->admin)->post(route('settings.restore.upload'), []);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors('backup_file');
    }

    public function test_upload_restore_fails_validation_with_wrong_file_type()
    {
        $file = UploadedFile::fake()->create('backup.txt', 100, 'text/plain');
        
        $response = $this->actingAs($this->admin)->post(route('settings.restore.upload'), [
            'backup_file' => $file,
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors('backup_file');
    }
}