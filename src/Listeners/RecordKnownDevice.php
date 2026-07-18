<?php

namespace Smony\FilamentUserSessions\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Notification;
use Smony\FilamentUserSessions\Models\KnownDevice;
use Smony\FilamentUserSessions\Models\Session;
use Smony\FilamentUserSessions\Notifications\NewDeviceLogin;

class RecordKnownDevice
{
    public function handle(Login $event): void
    {
        $fingerprint = Session::describeUserAgent(request()->userAgent());

        $device = KnownDevice::query()->firstOrNew([
            'user_id' => $event->user->getAuthIdentifier(),
            'fingerprint' => $fingerprint,
        ]);

        $isNewDevice = ! $device->exists;

        if ($isNewDevice) {
            $device->first_seen_at = now();
        }

        $device->last_seen_at = now();
        $device->save();

        if ($isNewDevice && config('filament-user-sessions.notify_on_new_device', true)) {
            $this->notifyRecipients($event->user, $fingerprint);
        }
    }

    private function notifyRecipients(Authenticatable $user, string $fingerprint): void
    {
        $resolverClass = config('filament-user-sessions.new_device_notification_recipients');

        if (blank($resolverClass)) {
            return;
        }

        $recipients = collect(app($resolverClass)->resolve($user));

        Notification::send($recipients, new NewDeviceLogin($user, $fingerprint, request()->ip()));
    }
}
