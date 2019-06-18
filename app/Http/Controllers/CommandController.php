<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CommandController extends Controller
{
    public function execute(Request $request)
    {
        $validCommands = ['create:snapshots','delete:snapshots'];
        $logType = "info";
        $message = 'Command received' . PHP_EOL;

        if ($request['token']!== env('SLACK_COMMAND_TOKEN')) {
            $message .= 'Invalid SLACK_COMMAND_TOKEN' . PHP_EOL;
            $logType = "warning";
            Log::$logType($message);
            return response("Invalid TOKEN", 419);
        }

        $args = explode(' ',$request['text']);
        $message .= "```" . json_encode($args) . "```" . PHP_EOL;
        $command_args = [];
        if (in_array($args[0], $validCommands)) {
            $message .= "Valid command received: $args[0]" . PHP_EOL;
            $options = array_slice($args, 1);
            foreach ($options as $option) {
                if (substr($option, 0, 2) === "--") {
                    $has_val=strpos($option, '=');
                    if ($has_val) {
                        $command_args[substr($option,0,$has_val)]=substr($option, $has_val+1);
                    } else {
                        $command_args[$option]=true;
                    }
                }
                else {
                    $command_args[''] = $option;
                }
            }
            try
            {
                $message .= "Calling artisan command" . PHP_EOL;
                Artisan::call($args[0], $command_args);
            }
            catch (Exception $ex) {
                $logType = "error";
                $message .= "An Exception Occurred: " . $ex->getMessage() . PHP_EOL;
                $message .= "Tried running artisan command: " . $args[0] . ", with options: ```" . json_encode($command_args) . "```" . PHP_EOL;
                Log::$logType($message);
                return response('Error, most likely invalid options', 422);
            }
        }
        else {
            $logType = "error";
            $message .= "Invalid command: $args[0], valid commands are " . implode($validCommands, ' ');
            Log::$logType($message);
            return response('Error, most likely invalid options', 422);
        }

        Log::$logType($message);
        return response("Command executed successfully", 200);
    }
}
