<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Console\Commands\RunScheduledBackup;

class Kernel extends ConsoleKernel
{
    protected $commands = [
         Commands\RunScheduledBackup::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Executa o comando de verificação de backup diariamente às 02:00
        $schedule->command(RunScheduledBackup::class)
                 ->dailyAt('11:0') // Ajuste o horário conforme necessário (ex: '02:00')
                 ->timezone('America/Sao_Paulo') // Garante o fuso horário correto
                 ->withoutOverlapping(60) // Evita que rode múltiplas vezes se demorar mais que 1 min
                 ->onSuccess(function () {
                     Log::info('[Backup Agendado - Kernel] Execução do comando agendado concluída com sucesso.');
                 })
                 ->onFailure(function () {
                     Log::error('[Backup Agendado - Kernel] Falha na execução do comando agendado.');
                     // Opcional: Notificar um canal de erro específico aqui
                 });

        // Mantém a limpeza diária de backups antigos (ex: 03:00)
        $schedule->command('backup:clean')
                 ->dailyAt('03:00')
                 ->timezone('America/Sao_Paulo');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}