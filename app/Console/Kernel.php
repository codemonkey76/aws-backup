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
        $schedule
            ->command('delete:snapshots', ['--frequency=hourly', '--owner=939600349024'])
            ->daily()
            ->appendOutputTo(storage_path('logs/schedule.log'))
            ->emailOutputOnFailure((config('app.contact')));

        $schedule
            ->command('delete:snapshots', ['--frequency=daily', '--owner=939600349024'])
            ->weekly()
            ->appendOutputTo(storage_path('logs/schedule.log'))
            ->emailOutputOnFailure(config('app.contact'));

        $schedule
            ->command('delete:snapshots', ['--frequency=weekly', '--owner=939600349024'])
            ->monthly()
            ->appendOutputTo(storage_path('logs/schedule.log'))
            ->emailOutputOnFailure(config('app.contact'));

        $schedule
            ->command('create:snapshots', ['--tag=hourly'])
            ->hourly()
            ->appendOutputTo(storage_path('logs/schedule.log'))
            ->emailOutputOnFailure(config('app.contact'));

        $schedule
            ->command('create:snapshots', ['--tag=daily'])
            ->daily()
            ->appendOutputTo(storage_path('logs/schedule.log'))
            ->emailOutputOnFailure(config('app.contact'));

        $schedule
            ->command('create:snapshots', ['--tag=weekly'])
            ->weekly()
            ->appendOutputTo(storage_path('logs/schedule.log'))
            ->emailOutputOnFailure(config('app.contact'));
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
