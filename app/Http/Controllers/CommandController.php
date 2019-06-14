<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CommandController extends Controller
{
    public function execute(Request $request)
    {
        $validCommands = ['createSnapshots','deleteSnapshots'];
        $message = '';

        if ($request['token']!== env('SLACK_COMMAND_TOKEN')) {
            return response(419);
        }
        $args = explode(' ',$request['text']);

        if (in_array(
            Str::camel($args[0]), $validCommands)) {
            $message .= "Valid command received: $args[0]";
        }
        else {
            $message .= "Invalid command: $args[0], valid commands are " . implode($validCommands, ' ');
        }

        Log::info($args);
        return $message;
    }
}
