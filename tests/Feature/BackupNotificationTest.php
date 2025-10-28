<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Usuario;
use App\Notifications\CustomBackupWasSuccessfulNotification;
use Spatie\Backup\Events\BackupWasSuccessful;

class BackupNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function administrador_recebe_notificacao_apos_backup_manual_bem_sucedido()
    {
        Storage::fake('backups_test');
        config(['backup.backup.destination.disks' => ['backups_test']]);
        config(['backup.notifications.notifications.'.CustomBackupWasSuccessfulNotification::class => ['mail']]); 
        $admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);

        Notification::fake(); 
        // event(new BackupWasSuccessful($backupDestinationMock)); 
        Artisan::call('backup:run', ['--only-db' => true, '--disable-notifications' => false]); 
        Notification::assertSentTo(
            [$admin], 
            CustomBackupWasSuccessfulNotification::class
        );
    }
}