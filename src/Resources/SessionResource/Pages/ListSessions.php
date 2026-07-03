<?php

namespace Smony\FilamentUserSessions\Resources\SessionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Smony\FilamentUserSessions\Resources\SessionResource;

class ListSessions extends ListRecords
{
    protected static string $resource = SessionResource::class;
}
