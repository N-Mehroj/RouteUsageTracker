<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Usage Tracker Settings
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the Route Usage Tracker
    | package.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable route tracking. If false, no routes will be tracked.
    |
    */
    'enabled' => env('ROUTE_USAGE_TRACKER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Ignored Routes
    |--------------------------------------------------------------------------
    |
    | These routes will not be tracked. Can be patterns or exact route names.
    |
    */
    'ignored_routes' => [
        'telescope.*',
        'horizon.*',
        'debugbar.*',
        '_debugbar/*',
        'livewire.*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignored Methods
    |--------------------------------------------------------------------------
    |
    | These HTTP methods will not be tracked.
    |
    */
    'ignored_methods' => [
        'HEAD',
        'OPTIONS',
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | The database connection where route usage data will be stored.
    | If null, the default connection will be used.
    |
    */
    'database_connection' => env('ROUTE_USAGE_TRACKER_DB_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | The table name where route usage data will be stored.
    |
    */
    'table_name' => env('ROUTE_USAGE_TRACKER_TABLE', 'route_usage'),

    /*
    |--------------------------------------------------------------------------
    | Auto Cleanup
    |--------------------------------------------------------------------------
    |
    | Automatically clean up old data. 'days' - how many days old
    | data should be deleted. If 0, no cleanup will be performed.
    |
    */
    'auto_cleanup' => [
        'enabled' => env('ROUTE_USAGE_TRACKER_AUTO_CLEANUP', false),
        'days' => env('ROUTE_USAGE_TRACKER_CLEANUP_DAYS', 365),
    ],
];
