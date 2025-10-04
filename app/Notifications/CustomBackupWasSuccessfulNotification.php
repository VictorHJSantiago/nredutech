<?php
/*
namespace App\Notifications;

use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification as SpatieBackupWasSuccessfulNotification;
use App\Mail\BackupSuccessfulMail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomBackupWasSuccessfulNotification extends SpatieBackupWasSuccessfulNotification
{
    /**
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     
    public function toMail($notifiable): MailMessage|SuccessfulMail
    {
        return (new BackupSuccessfulMail($this->event))->to($notifiable->routes['mail']);
    }
}