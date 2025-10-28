<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Notifications\CustomBackupWasSuccessfulNotification;
use Spatie\Backup\Events\BackupWasSuccessful; 
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;

class CustomBackupWasSuccessfulNotificationTest extends TestCase
{
    /** @test */
    public function notificacao_de_backup_bem_sucedido_gera_mensagem_de_email_correta()
    {
        Config::set('backup.backup.name', 'NREduTech');
        $backupDestinationMock = \Mockery::mock('Spatie\Backup\BackupDestination\BackupDestination');
        $backupDestinationMock->shouldReceive('backupName')->andReturn('NREduTech');
        $backupDestinationMock->shouldReceive('diskName')->andReturn('backups_test');
        $backupDestinationMock->shouldReceive('newestBackup')
            ->andReturn(\Mockery::mock('Spatie\Backup\BackupDestination\Backup', ['path' => 'backup.zip', 'sizeInBytes' => 1024 * 1024, 'date' => now()]));
        $backupDestinationMock->shouldReceive('backups')->andReturn(collect([]));


        $event = new BackupWasSuccessful($backupDestinationMock);
        $notification = new CustomBackupWasSuccessfulNotification($event);
        $notifiable = new \App\Models\Usuario(); 
        $mailMessage = $notification->toMail($notifiable);
        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals('NREduTech - Backup Realizado com Sucesso', $mailMessage->subject);
        $this->assertStringContainsString('O backup da aplicação NREduTech foi concluído com sucesso.', $mailMessage->render());
        $this->assertStringContainsString('Gerenciar Backups', $mailMessage->render()); 

        \Mockery::close();
    }
}