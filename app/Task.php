<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public static $frequencies = ['hourly', 'twiceDaily', 'daily', 'weekdays', 'weekends', 'weekly', 'twiceMonthly', 'monthly', 'quarterly', 'yearly'];
}