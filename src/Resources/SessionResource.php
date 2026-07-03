<?php

namespace Smony\FilamentUserSessions\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Jenssegers\Agent\Agent;
use Smony\FilamentUserSessions\Models\Session;
use Smony\FilamentUserSessions\Resources\SessionResource\Pages\ListSessions;
use UnitEnum;

class SessionResource extends Resource
{
    protected static ?string $model = Session::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static ?string $navigationLabel = 'User Sessions';

    protected static string|UnitEnum|null $navigationGroup = 'Security';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('last_activity', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->default('Guest'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP address')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Device')
                    ->formatStateUsing(function (?string $state): string {
                        if (blank($state)) {
                            return 'Unknown';
                        }

                        $agent = new Agent;
                        $agent->setUserAgent($state);

                        return sprintf(
                            '%s on %s',
                            $agent->browser() ?: 'Unknown browser',
                            $agent->platform() ?: 'Unknown platform',
                        );
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Last active')
                    ->formatStateUsing(fn (int $state) => \Carbon\Carbon::createFromTimestamp($state)->diffForHumans())
                    ->sortable(),

                Tables\Columns\IconColumn::make('current_device')
                    ->label('This device')
                    ->state(fn (Session $record): bool => $record->isCurrentDevice())
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\Filter::make('online_now')
                    ->label('Online now')
                    ->query(fn ($query) => $query->where(
                        'last_activity',
                        '>=',
                        now()->subMinutes(config('filament-user-sessions.online_threshold_minutes', 5))->timestamp,
                    )),
            ])
            ->recordActions([
                Action::make('revoke')
                    ->label('Revoke')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Session $record): bool => ! $record->isCurrentDevice())
                    ->action(fn (Session $record) => $record->delete()),
            ])
            ->toolbarActions([
                BulkAction::make('revoke')
                    ->label('Revoke selected')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($records) => $records->each->delete()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSessions::route('/'),
        ];
    }
}
