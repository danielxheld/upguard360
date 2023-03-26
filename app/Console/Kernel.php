<?php

namespace App\Console;

use App\Jobs\ProcessMonitorCheck;
use App\Models\Monitor;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $monitors = Monitor::all();
        foreach ($monitors as $monitor) {
            $interval = $monitor->interval;
            $schedule->job(new ProcessMonitorCheck($monitor))->$interval();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }


}
