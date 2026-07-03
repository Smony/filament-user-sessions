<?php

namespace Smony\FilamentUserSessions\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    protected $casts = [
        'last_activity' => 'integer',
    ];

    public function getTable(): string
    {
        return config('filament-user-sessions.sessions_table', 'sessions');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function lastActiveAt(): Carbon
    {
        return Carbon::createFromTimestamp($this->last_activity);
    }

    public function isCurrentDevice(): bool
    {
        return $this->getKey() === session()->getId();
    }
}
