<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\RunScheduledBackup::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('backup:run --only-db')->daily()->at('01:00'); 
        $schedule->command('backup:monitor')->daily()->at('02:00');
        $schedule->command('backup:clean')->daily()->at('03:00');
        
        $schedule->command('nre:run-scheduled-backup')->daily()->at('01:30'); 
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}