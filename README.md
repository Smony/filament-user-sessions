# Filament User Sessions

See every active session across your app — who's logged in, from where, on what device — and force-logout anyone with one click, right from your Filament admin panel.

## Requirements

- Laravel with `SESSION_DRIVER=database` (this plugin reads the `sessions` table, so file/cookie/array drivers won't work)
- Filament v3

## Installation

```bash
composer require smony/filament-user-sessions
```

Make sure you have the sessions table migration (Laravel ships one, or generate it):

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

## Configuration

Optionally publish the config to change the sessions table name or the "online" threshold:

```bash
php artisan vendor:publish --tag=filament-user-sessions-config
```

## License

MIT
# filament-user-sessions
