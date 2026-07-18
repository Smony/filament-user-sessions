<?php

namespace Smony\FilamentUserSessions\Models;

use Illuminate\Database\Eloquent\Model;

class KnownDevice extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'fingerprint',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('filament-user-sessions.known_devices_table', 'session_known_devices');
    }
}
