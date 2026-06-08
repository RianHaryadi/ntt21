<?php

namespace App\Filament\Resources\TravelChatSessionResource\Pages;

use App\Filament\Resources\TravelChatSessionResource;
use Filament\Resources\Pages\ListRecords;

class ListTravelChatSessions extends ListRecords
{
    protected static string $resource = TravelChatSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
