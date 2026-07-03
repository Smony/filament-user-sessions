<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sessions table
    |--------------------------------------------------------------------------
    |
    | This plugin only works when SESSION_DRIVER=database, since that is
    | the only driver that stores sessions in a queryable table.
    |
    */
    'sessions_table' => 'sessions',

    /*
    |--------------------------------------------------------------------------
    | Online threshold (minutes)
    |--------------------------------------------------------------------------
    |
    | A session is considered "online now" if its last activity happened
    | within this many minutes.
    |
    */
    'online_threshold_minutes' => 5,
];
