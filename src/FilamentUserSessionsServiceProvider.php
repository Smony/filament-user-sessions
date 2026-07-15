<?php

namespace Smony\FilamentUserSessions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Smony\FilamentUserSessions\Models\Session;
use Smony\FilamentUserSessions\Policies\SessionPolicy;

class FilamentUserSessionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/filament-user-sessions.php', 'filament-user-sessions');
    }

    public function boot(): void
    {
        // Registered here so host apps can override it by calling
        // Gate::policy(Session::class, ...) in their own AuthServiceProvider,
        // which boots after package service providers.
        Gate::policy(Session::class, SessionPolicy::class);

        $this->publishes([
            __DIR__.'/../config/filament-user-sessions.php' => config_path('filament-user-sessions.php'),
        ], 'filament-user-sessions-config');
    }
}
