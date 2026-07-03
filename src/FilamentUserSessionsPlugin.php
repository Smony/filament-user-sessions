<?php

namespace Smony\FilamentUserSessions;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Smony\FilamentUserSessions\Resources\SessionResource;
use Smony\FilamentUserSessions\Widgets\OnlineSessionsWidget;

class FilamentUserSessionsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-user-sessions';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                SessionResource::class,
            ])
            ->widgets([
                OnlineSessionsWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
