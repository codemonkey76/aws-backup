<?php

namespace App\Console\Commands;

use Aws\Ec2\Ec2Client;
use Illuminate\Console\Command;

class CreateSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        foreach ($volumes as $vol) {
            $obj = (Object)$vol;;
            $attachment = (Object)$obj->Attachments[0];
            dump($attachment->InstanceId);

            //Get instance and ensure it is running
            $status = $ec2->describeInstances(['InstanceIds' => [$attachment->InstanceId]]);

            $state = data_get($status->toArray(), 'Reservations.0.Instances.0.State.Name');;

            if ($state === "running") {
                //Only snapshot running instances
            }
        }
    }
}
