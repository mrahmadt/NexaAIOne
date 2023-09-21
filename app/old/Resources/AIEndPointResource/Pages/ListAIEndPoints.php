<?php

namespace App\Filament\Resources\AIEndPointResource\Pages;

use App\Filament\Resources\AIEndPointResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAIEndPoints extends ListRecords
{
    protected static string $resource = AIEndPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
