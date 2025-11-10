<?php

namespace App\Notifications;

use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification as SpatieBackupWasSuccessfulNotification;
use Spatie\Backup\Events\BackupWasSuccessful;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\Helpers\Format;
use App\Mail\BackupSuccessfulMail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomBackupWasSuccessfulNotification extends SpatieBackupWasSuccessfulNotification
{
    public function toMail(object $notifiable): BackupSuccessfulMail
    {
        $userName = $notifiable->nome_completo ?? $notifiable->email;
        $backupInitiatedBy = auth()->check() ? auth()->user()->nome_completo : 'Sistema';
        $backupDetails = $this->getBackupDetails();

        return (new BackupSuccessfulMail($userName, $backupInitiatedBy, $backupDetails))
            ->to($notifiable->email);
    }

    protected function getBackupDetails(): array
    {
        $backupDestination = $this->event->backupDestination();
        $backups = Backup::all();

        return [
            'appName' => config('app.name'),
            'backupName' => $backupDestination->backupName(),
            'diskName' => $backupDestination->diskName(),
            'latestBackupSize' => $backupDestination->sizeInBytes() ? Format::humanReadableSize($backupDestination->sizeInBytes()) : 'N/A',
            'backupCount' => $backups->count(),
            'totalStorageUsed' => Format::humanReadableSize($backups->size()),
            'latestBackupDate' => $backups->isNotEmpty() ? $backups->first()->date()->format('d/m/Y H:i') : 'N/A',
            'oldestBackupDate' => $backups->isNotEmpty() ? $backups->last()->date()->format('d/m/Y H:i') : 'N/A',
        ];
    }
}