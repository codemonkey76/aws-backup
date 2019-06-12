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
    protected $signature = 'delete:snapshots';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ec2 = new Ec2Client(['version' => '2016-11-15', 'region' => 'ap-southeast-2']);
        $results = $ec2->describeSnapshots([
            'OwnerIds' => ['939600349024']
        ]);

        $all = $results->toArray();

        $snapshots = $all['Snapshots'];


        foreach ($snapshots as $snap) {
            $obj = (Object)$snap;

            $snapData = new Carbon($obj->StartTime->jsonSerialize());
            $old = (new Carbon())->subDays(30);

            if ($snapData < $old) {
                $id = $obj->SnapshotId;
                $this->info('Deleting Snapshot: ' . $id);
                try {
                    $ec2->deleteSnapshot(['SnapshotId' => $id]);
                    sleep(1);
                } catch (Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        }
    }
}
