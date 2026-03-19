<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
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

    // audit log helper
    public static function logActivity($action, $status, $userId = null, $metadata = [])
    {
        if (!config('voting.log_all_attempts', true)) {
            return;
        }

        AuditLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'status' => $status,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'fingerprint' => request('fingerprint') ?? null, // if passed
            'metadata' => $metadata,
        ]);
    }
}
