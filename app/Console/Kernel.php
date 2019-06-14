<?php

namespace App\Console;

use App\Task;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Schema;

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
        if (Schema::hasTable('tasks')) {
            $tasks = Task::all();

            foreach ($tasks as $task) {
                if (in_array($task->frequency, Task::$frequencies)) {
                    $schedule
                        ->command($task->command, $task->args)
                        ->${$task->frequency}()
                        ->appendOutputTo(storage_path('logs/schedule.log'))
                        ->emailOutputOnFailure(config('app.contact'));
                }
            }
        }
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
