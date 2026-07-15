<?php

namespace Smony\FilamentUserSessions\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Smony\FilamentUserSessions\Models\Session;
use Smony\FilamentUserSessions\Resources\SessionResource;

class OnlineSessionsWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return SessionResource::canViewAny();
    }

    protected function getStats(): array
    {
        $threshold = now()->subMinutes(config('filament-user-sessions.online_threshold_minutes', 5))->timestamp;

        $onlineCount = Session::query()
            ->where('last_activity', '>=', $threshold)
            ->count();

        $totalCount = Session::query()->count();

        return [
            Stat::make('Online now', $onlineCount)
                ->description('Active in the last '.config('filament-user-sessions.online_threshold_minutes', 5).' minutes')
                ->color('success'),

            Stat::make('Total sessions', $totalCount)
                ->color('gray'),
        ];
    }
}
