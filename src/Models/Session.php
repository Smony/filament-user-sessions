<?php

namespace Smony\FilamentUserSessions\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Agent\Agent;

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

    /**
     * A human-readable device label, also used as the fingerprint for
     * "known device" tracking (see isNewDevice()).
     */
    public function device(): string
    {
        return static::describeUserAgent($this->user_agent);
    }

    public static function describeUserAgent(?string $userAgent): string
    {
        if (blank($userAgent)) {
            return 'Unknown';
        }

        $agent = new Agent;
        $agent->setUserAgent($userAgent);

        return sprintf(
            '%s on %s',
            $agent->browser() ?: 'Unknown browser',
            $agent->platform() ?: 'Unknown platform',
        );
    }

    /**
     * Whether this session's device was first seen for this user within the
     * configured window, based on the session_known_devices table recorded
     * on login (see Listeners\RecordKnownDevice).
     */
    public function isNewDevice(): bool
    {
        if (blank($this->user_id)) {
            return false;
        }

        $windowHours = config('filament-user-sessions.new_device_window_hours', 24);

        return KnownDevice::query()
            ->where('user_id', $this->user_id)
            ->where('fingerprint', $this->device())
            ->where('first_seen_at', '>=', now()->subHours($windowHours))
            ->exists();
    }
}
