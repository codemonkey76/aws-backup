<?php

namespace App\Console;

use App\Task;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        if (stripos((string) shell_exec('ps xf | grep \'[q]ueue:work\''), 'artisan queue:work') === false) {
            $schedule->command('queue:work --stop-when-empty')->everyMinute()->appendOutputTo(storage_path() . '/logs/scheduler.log');
        }
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?";

        try
        {
            $db = DB::select($query, [env('DB_DATABASE')]);
            if ( ! empty($db))
            {
                if (Schema::hasTable('tasks'))
                {
                    $tasks = Task::all();

                    foreach ($tasks as $task)
                    {
                        if (in_array($task->frequency, Task::$frequencies))
                        {
                            $schedule
                                ->command($task->command, explode(' ', $task->args))
                                ->{$task->frequency}()
                                ->appendOutputTo(storage_path('logs/schedule.log'))
                                ->emailOutputOnFailure(config('app.contact'));
                        }
                    }
                } else
                {
                    Log::Error("Tasks table doesn't exist, did you forget to run php artisan migrate?");
                }
            }
            else
            {
                Log::Error("Database does not exist yet, check .env or create your DB");
            }
        } catch (Exception $ex) {
            Log::Error("Could not connect to database, maybe check your credentials in .env :" . $ex->getMessage());
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
