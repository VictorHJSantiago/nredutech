<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackupSuccessfulMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $backupInitiatedBy;
    public $appName;
    public $backupName;
    public $diskName;
    public $latestBackupSize;
    public $backupCount;
    public $totalStorageUsed;
    public $latestBackupDate;
    public $oldestBackupDate;

    public function __construct(string $userName, string $backupInitiatedBy, array $backupDetails)
    {
        $this->userName = $userName;
        $this->backupInitiatedBy = $backupInitiatedBy;
        $this->appName = $backupDetails['appName'];
        $this->backupName = $backupDetails['backupName'];
        $this->diskName = $backupDetails['diskName'];
        $this->latestBackupSize = $backupDetails['latestBackupSize'];
        $this->backupCount = $backupDetails['backupCount'];
        $this->totalStorageUsed = $backupDetails['totalStorageUsed'];
        $this->latestBackupDate = $backupDetails['latestBackupDate'];
        $this->oldestBackupDate = $backupDetails['oldestBackupDate'];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'NREduTech - Backup Realizado com Sucesso',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.backup-successful',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}