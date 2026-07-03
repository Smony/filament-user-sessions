<?php

namespace Smony\FilamentUserSessions;

use Illuminate\Support\ServiceProvider;

class FilamentUserSessionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/filament-user-sessions.php', 'filament-user-sessions');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/filament-user-sessions.php' => config_path('filament-user-sessions.php'),
        ], 'filament-user-sessions-config');
    }
}
