<?php

namespace App\Http\Controllers;

use Aws\Ec2\Ec2Client;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
//        $this->deleteSnaps();
        $this->createSnapshots();
//        return view('home');
    }
    public function deleteSnaps()
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
                dump('Deleting Snapshot: ' . $id);
                try {
                    $ec2->deleteSnapshot(['SnapshotId' => $id]);
                    sleep(1);
                } catch (Exception $e) {
                   dump($e->getMessage());
                }
            }
        }
    }

    public function createSnapshots()
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
            $instance = (Object)($status->toArray()['Reservations'][0]['Instances'][0]);
            dd($instance);
            $state = (Object)$instance->State;

            if ($state->Name == "running") {
                //Only snapshot running instances
            }
        }
    }
}
