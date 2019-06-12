<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('delete:snapshots', ['--frequency=hourly', '--owner=939600349024'])->daily();
        $schedule->command('delete:snapshots', ['--frequency=daily', '--owner=939600349024'])->weekly();
        $schedule->command('delete:snapshots', ['--frequency=weekly', '--owner=939600349024'])->monthly();
        $schedule->command('create:snapshots', ['--tag=hourly'])->hourly();
        $schedule->command('create:snapshots', ['--tag=daily'])->daily();
        $schedule->command('create:snapshots', ['--tag=weekly'])->weekly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
