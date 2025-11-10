<?php

namespace App\Notifications;

use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification as SpatieBackupWasSuccessfulNotification;
use App\Mail\BackupSuccessfulMail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomBackupWasSuccessfulNotification extends SpatieBackupWasSuccessfulNotification
{
    public function toMail(): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject(trans('backup::notifications.backup_successful_subject', ['application_name' => $this->applicationName()]))
            ->line(trans('backup::notifications.backup_successful_body', ['application_name' => $this->applicationName(), 'disk_name' => $this->diskName()]));

        $this->backupDestinationProperties()->each(function ($value, $name) use ($mailMessage) {
            $mailMessage->line("{$name}: {$value}");
        });

        return $mailMessage;
    }
}