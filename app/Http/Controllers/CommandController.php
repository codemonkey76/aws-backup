<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CommandController extends Controller
{
    public function execute(Request $request)
    {
        $validCommands = ['create:snapshots','delete:snapshots'];
        $message = '';

        if ($request['token']!== env('SLACK_COMMAND_TOKEN')) {
            return response(419);
        }
        $args = explode(' ',$request['text']);

        $command_args = [];
        if (in_array($args[0], $validCommands)) {
            $message .= "Valid command received: $args[0]";
            $options = array_slice($args, 1);
            foreach ($options as $option) {
                if (substr($option, 0, 2) === "--") {
                    $has_val=strpos($option, '=');
                    if ($has_val) {
                        $command_args[substr($option,2,$has_val-2)]=substr($option, $has_val+1);
                    } else {
                        $command_args[substr($option, 2)]=true;
                    }
                }
                else {
                    $command_args[''] = $option;
                }
            }
            Artisan::call($args[0], $command_args);
        }
        else {
            $message .= "Invalid command: $args[0], valid commands are " . implode($validCommands, ' ');
        }

        Log::info($args);
        return $message;
    }
}
