<?php

namespace App\Console\Commands;

use Aws\Ec2\Ec2Client;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:snapshots {--tag=hourly}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Snapshots';

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

        //Get volumes that are attached to instances and have tag:Environment=Production
        $results = $ec2->describeVolumes([
            [
                'Filter' => [
                    'Name' => 'attachment.status',
                    'Values' => ['attached']
                ],
            ],
            [
                'Filter' => [
                    'Name' => 'tag:Environment',
                    'Values' => ['Production']
                ]
            ]
        ]);

        $all = $results->toArray();
        $volumes = $all['Volumes'];
        $tag = $this->option('tag');

        foreach ($volumes as $vol) {
            $obj = (Object)$vol;
            //dd($vol);
            //If there are no attachments, they should not be in the list, because we specified attachment.status=attached
            //could be an api bug?
            if (empty($obj->Attachments)) break;
            $attachment = (Object)$obj->Attachments[0];

            //Get instance and ensure it is running
            $results = $ec2->describeInstances(['InstanceIds' => [$attachment->InstanceId]])->toArray();
            $instance = data_get($results, 'Reservations.0.Instances.0');
            $instanceName = collect($instance['Tags'])->where('Key', 'Name')->first()['Value'];
            $volumeBlock = $attachment->Device;
            $date = (new Carbon())->format('Ymd');

            $state = data_get($instance, 'State.Name');
            $this->info("State = $state");
            if ($state === "running") {
                //Only snapshot running instances
                $this->info("Creating snapshot Automated backup of $instanceName-$volumeBlock-$date");
                $snap = $ec2->createSnapshot([
                    'Description' => "Automated backup of $instanceName-$volumeBlock-$date",
                    'VolumeId' => "$obj->VolumeId"
                ]);
                $snap_id = $snap->toArray()['SnapshotId'];

                sleep(1);

                $this->info('Creating Tag');
                $ec2->createTags([
                    'Resources' => [
                        $snap_id,
                    ],
                    'Tags' => [
                        [
                            'Key' => 'Backup',
                            'Value' => $tag,
                        ],
                    ],
                ]);
            }
        }
    }
}