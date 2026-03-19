<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Face Recognition Threshold
    |--------------------------------------------------------------------------
    |
    | Minimum similarity score (0-1) required for face verification.
    | Higher values are stricter but may cause false rejections.
    | Recommended range: 0.5 - 0.7
    |
    */
    'face_threshold' => env('VOTE_FACE_THRESHOLD', 0.6),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Maximum number of voting attempts per minute per user/IP.
    |
    */
    'rate_limit_attempts' => env('VOTE_RATE_LIMIT', 5),
    'rate_limit_decay' => 1, // minutes

    /*
    |--------------------------------------------------------------------------
    | Suspicious Activity Logging
    |--------------------------------------------------------------------------
    |
    | Log all security events for audit.
    |
    */
    'log_all_attempts' => env('VOTE_LOG_ALL', true),
];
