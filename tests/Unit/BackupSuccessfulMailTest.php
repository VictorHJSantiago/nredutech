<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Mail\BackupSuccessfulMail;
use Illuminate\Support\Facades\Mail;

class BackupSuccessfulMailTest extends TestCase
{
    /** @test */
    public function mailable_de_backup_bem_sucedido_e_construido_corretamente()
    {
        $userName = 'Admin Teste';
        $initiatedBy = 'Sistema (Agendado)';
        $details = [
            'appName' => 'NREduTech',
            'backupName' => 'backup_test.zip',
            'diskName' => 'backups',
            'latestBackupSize' => '10 MB',
            'backupCount' => 5,
            'totalStorageUsed' => '50 MB',
            'latestBackupDate' => '28/10/2025 10:00',
            'oldestBackupDate' => '23/10/2025 10:00',
        ];

        $mailable = new BackupSuccessfulMail($userName, $initiatedBy, $details);
        $mailable->assertHasSubject('NREduTech - Backup Realizado com Sucesso');
                $mailable->assertSeeInHtml($userName);
        $mailable->assertSeeInHtml('10 MB'); 
        $mailable->assertSeeInHtml('Sistema (Agendado)');
        $mailable->assertSeeInHtml(route('settings')); 
    }
}