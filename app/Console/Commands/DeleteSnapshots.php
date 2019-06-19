<?php

namespace App\Console\Commands;

use Aws\Ec2\Ec2Client;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:snapshots {--frequency=hourly} {--owner=} {--region=} {--age=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old Snapshots';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        $log = "";
        $logType = "info";

        $owner = $this->option('owner') ?? env('AWS_DEFAULT_OWNER');
        $region = $this->option('region') ?? env('AWS_DEFAULT_REGION');
        $age =  $this->option('age');
        $this->info("Running DeleteSnapshots");
        $log .= "Running DeleteSnapshots" . PHP_EOL;
        $ec2 = new Ec2Client(['version' => '2016-11-15', 'region' => $region]);
        $frequency = $this->option('frequency');

        $results = null;
        if ($frequency==="untagged")
        {
            try {
                $results = $ec2->describeSnapshots([
                    'OwnerIds' => [$owner],
                ]);
            }
            catch (Exception $ex)
            {
                $log .= $ex->getMessage() . PHP_EOL;
                $logType = "error";
                $this->error($ex->getMessage());
            }

        }
        else
        {
            try {
                $results = $ec2->describeSnapshots([
                    'OwnerIds' => [$owner],
                    'Filters'  => [
                        [
                            'Name'   => 'tag:Backup',
                            'Values' => [$frequency],
                        ],
                    ],
                ]);
            }
            catch (Exception $ex) {
                $log .= $ex->getMessage() . PHP_EOL;
                $logType = "error";
                $this->error($ex->getMessage());
            }

        }

        $old = new Carbon();

        $this->info('Frequency: ' . $frequency);
        $log .= 'Frequency: ' . $frequency . PHP_EOL;

        switch ($frequency) {
            case 'daily':
                $num = $age ?? 8;
                $msg = "Snapshot is old if older than $num days";
                $this->info($msg);
                $log .= $msg . PHP_EOL;
                $old = $old->subDays($num);
                break;
            case 'weekly':
                $num = $age ?? 4;
                $msg = "Snapshot is old if older than $num weeks";
                $this->info($msg);
                $log .= $msg . PHP_EOL;
                $old = $old->subweeks($num);
                break;
            case 'hourly':
                $num = $age ?? 30;
                $msg = "Snapshot is old if older than $num hours";
                $this->info($msg);
                $log .= $msg . PHP_EOL;
                $old = $old->subHours($num);
                break;
        }

        if ($results === null)
        {
            $log .= "No Snapshots found" . PHP_EOL;
            $logType = "error";
            $this->error("No Snapshots found");
            return;
        }

        $all = $results->toArray();

        $snapshots = $all['Snapshots'];

        $this->info("Found " . count($snapshots) . " snapshots with Frequency: " . $frequency);
        $log .= "Found " . count($snapshots) . " snapshots with Frequency: " . $frequency . PHP_EOL;


        $oldSnaps = 0;
        $newSnaps = 0;
        $deleted = 0;
        foreach ($snapshots as $snap) {
            $obj = (Object)$snap;

            $snapDate = new Carbon($obj->StartTime->jsonSerialize());

            if ($snapDate < $old) {
                $oldSnaps += 1;
                $id = $obj->SnapshotId;
                $this->info('Snapshot date: ' . $snapDate);
                $this->info('Deleting Snapshot: ' . $id);

                try {
                    $ec2->deleteSnapshot(['SnapshotId' => $id]);
                    $deleted += 1;
                    sleep(1);
                } catch (Exception $e) {
                    $this->error($e->getMessage());
                    $log .= "Error deleting snapshot: " . $id . PHP_EOL;
                    $log .= $e->getMessage() . PHP_EOL;
                    $logType = "error";
                }
            }
            else {
                $this->info('Snapshot date: ' . $snapDate . ' is newer than ' . $old);
                $newSnaps += 1;
            }
        }
        $log .= "Found $newSnaps new snapshots" . PHP_EOL;
        $log .= "Found $oldSnaps old snapshots" . PHP_EOL;
        $log .= "$deleted snapshots were successfully deleted" . PHP_EOL;

        Log::$logType($log);
    }
}
