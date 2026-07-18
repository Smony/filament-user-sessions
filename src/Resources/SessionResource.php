<?php

namespace Smony\FilamentUserSessions\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
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
                    ->formatStateUsing(fn (Session $record): string => $record->device())
                    ->wrap(),

                Tables\Columns\IconColumn::make('new_device')
                    ->label('')
                    ->tooltip('First seen for this user recently — worth checking')
                    ->state(fn (Session $record): bool => $record->isNewDevice())
                    ->icon(fn (bool $state): ?string => $state ? 'heroicon-o-exclamation-triangle' : null)
                    ->color('warning'),

                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Last active')
                    ->formatStateUsing(fn (Session $record) => $record->lastActiveAt()->diffForHumans())
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
                    ->authorize('delete')
                    ->action(fn (Session $record) => $record->delete()),
            ])
            ->toolbarActions([
                BulkAction::make('revoke')
                    ->label('Revoke selected')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->authorize('deleteAny')
                    ->action(fn ($records) => $records->reject->isCurrentDevice()->each->delete()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSessions::route('/'),
        ];
    }
}
