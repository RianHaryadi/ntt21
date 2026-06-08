<?php

namespace App\Filament\Resources\TravelChatSessionResource\Pages;

use App\Filament\Resources\TravelChatSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTravelChatSession extends ViewRecord
{
    protected static string $resource = TravelChatSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
