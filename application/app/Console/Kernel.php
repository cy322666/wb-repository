<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('wb:incomes 2')->dailyAt('10:00');
        $schedule->command('wb:incomes 2')->dailyAt('12:00');

        $schedule->command('wb:incomes 3')->dailyAt('14:00');
        $schedule->command('wb:incomes 3')->dailyAt('16:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        $this->load(__DIR__.'/Commands/WB');

        require base_path('routes/console.php');
    }
}
