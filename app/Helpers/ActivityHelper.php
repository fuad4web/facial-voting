<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActivityHelper
{
    public static function log($logName, $description, $causer = null, $properties = [])
    {
        DB::table('activity_logs')->insert([
            'log_name' => $logName,
            'description' => $description,
            'causer_type' => $causer ? get_class($causer) : null,
            'causer_id' => $causer ? $causer->id : null,
            'properties' => json_encode($properties),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
