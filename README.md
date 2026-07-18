# Filament User Sessions

[![Latest Version](https://img.shields.io/packagist/v/smony/filament-user-sessions.svg)](https://packagist.org/packages/smony/filament-user-sessions)
[![Total Downloads](https://img.shields.io/packagist/dt/smony/filament-user-sessions.svg)](https://packagist.org/packages/smony/filament-user-sessions)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)

See every active session across your app — who's logged in, from where, on what device — and force-logout anyone with one click, right from your Filament admin panel.

## Requirements

- Laravel with `SESSION_DRIVER=database` (this plugin reads the `sessions` table, so file/cookie/array drivers won't work)
- Filament v4 or v5

> Still on Filament v3? Use `composer require smony/filament-user-sessions:^1.0` instead.

## Installation

```bash
composer require smony/filament-user-sessions
```

Make sure you have the sessions table migration (Laravel ships one, or generate it), then migrate — this also creates the package's own `session_known_devices` table, used to flag new-device logins:

```bash
php artisan session:table
php artisan migrate
```

Register the plugin in your Panel Provider:

```php
use Smony\FilamentUserSessions\FilamentUserSessionsPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(FilamentUserSessionsPlugin::make());
}
```

## What you get

- A **Sessions** page listing every active session: user, IP address, parsed device/browser, last activity
- An **"Online now"** stat widget
- A **Revoke** action to instantly force-logout any session (except your own current one)
- Bulk revoke for cleaning up stale sessions
- A **"New device"** flag on sessions from a browser/platform combination not seen for that user before — useful for spotting a compromised account at a glance

## Configuration

Optionally publish the config to change the sessions table name, the "online" threshold, or the new-device detection window:

```bash
php artisan vendor:publish --tag=filament-user-sessions-config
```

## New device detection

On every login, the plugin records a fingerprint (browser + platform, e.g. "Chrome on Windows") for that user in the `session_known_devices` table, which — unlike `sessions` — persists across logout. A session is flagged **New** in the table if its fingerprint was first seen for that user within the last 24 hours (configurable via `new_device_window_hours`).

This is a coarse signal, not a strict security boundary: it doesn't fingerprint by IP (to avoid false positives from VPNs/mobile networks), so two different physical devices reporting the same browser/OS combination look identical.

### Notifying your security team

The first time (and only the first time) a device is seen for a user, whoever you configure as recipients gets notified via `Smony\FilamentUserSessions\Notifications\NewDeviceLogin`. The package has no way to know who your admins/security team are, so nothing is sent until you implement `Smony\FilamentUserSessions\Contracts\ResolvesNewDeviceRecipients` and point `new_device_notification_recipients` at it:

```php
use Illuminate\Contracts\Auth\Authenticatable;
use Smony\FilamentUserSessions\Contracts\ResolvesNewDeviceRecipients;

class SecurityTeamRecipients implements ResolvesNewDeviceRecipients
{
    public function resolve(Authenticatable $user): iterable
    {
        return User::where('is_superadmin', true)->get();
    }
}
```

```php
// config/filament-user-sessions.php
'new_device_notification_recipients' => \App\Notifications\SecurityTeamRecipients::class,
```

Configure delivery via `new_device_notification_channels` (default `['mail']`; add `database` for a Filament/Laravel database notification — requires `php artisan notifications:table` and `Notifiable` recipient models), or disable entirely with `notify_on_new_device => false`.

## Authorization

By default, any user who can access your panel can view all sessions and revoke any of them (except their own current one). If that's too permissive — e.g. you don't want a support-role admin to be able to kick out a superadmin — register your own policy for the `Session` model in your `AuthServiceProvider`:

```php
use Illuminate\Support\Facades\Gate;
use Smony\FilamentUserSessions\Models\Session;

Gate::policy(Session::class, YourSessionPolicy::class);
```

Your policy can implement `viewAny`, `view`, `delete`, and `deleteAny` — the same names Laravel and Filament use for standard CRUD abilities:

```php
class YourSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Session $session): bool
    {
        return $user->isSuperAdmin() || $session->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
```

Registering it in your own `AuthServiceProvider` overrides the package's permissive default, since app providers boot after package providers.

## License

MIT
