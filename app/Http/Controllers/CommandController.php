<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommandController extends Controller
{
    public function execute(Request $request)
    {
        Log::info($request->all());
        return "Running the command you requested";
    }
}
