<?php

namespace App\Console\Commands;

use Aws\Ec2\Ec2Client;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class DeleteSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:snapshots {--frequency=hourly} {--owner=}';

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
        $this->info("Running DeleteSnapshots");
        $ec2 = new Ec2Client(['version' => '2016-11-15', 'region' => 'ap-southeast-2']);
        $frequency = $this->option('frequency');

        $results = $ec2->describeSnapshots([
            'OwnerIds' => [$this->option('owner')],
            'Filters' => [
                [
                    'Name' => 'tag:Backup',
                    'Values' => [$frequency],
                ],
            ],
        ]);

        $old = new Carbon();

        $this->info('Frequency: ' . $frequency);
        switch ($frequency) {
            case 'daily':
                $this->info("Snapshot is old if older than 10 days");
                $old = $old->subDays(10);
                break;
            case 'weekly':
                $this->info("Snapshot is old if older than 4 weeks");
                $old = $old->subweeks(4);
                break;
            case 'hourly':
                $this->info("Snapshot is old if older than 48 hours");
                $old = $old->subHours(48);
                break;
        }

        $all = $results->toArray();

        $snapshots = $all['Snapshots'];
        $this->info("Found " . count($snapshots) . " snapshots with Frequency: " . $frequency);


        foreach ($snapshots as $snap) {
            $obj = (Object)$snap;

            $snapDate = new Carbon($obj->StartTime->jsonSerialize());

            if ($snapDate < $old) {
                $id = $obj->SnapshotId;
                $this->info('Snapshot date: ' . $snapDate);
                $this->info('Deleting Snapshot: ' . $id);
                try {
                    $ec2->deleteSnapshot(['SnapshotId' => $id]);
                    sleep(1);
                } catch (Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            else {
                $this->info('Snapshot date: ' . $snapDate . ' is newer than ' . $old);
            }
        }
    }
}
