<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommandController extends Controller
{
    public function execute(Request $request)
    {
        if ($request['token']!== env('SLACK_COMMAND_TOKEN')) {
            return response(419);
        }
        $args = explode(' ',$request['text']);
        return $args;
        Log::info($args);
//        return "Running the command you requested";
    }
}
