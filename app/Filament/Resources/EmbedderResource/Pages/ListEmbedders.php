<?php

namespace App\Filament\Resources\EmbedderResource\Pages;

use App\Filament\Resources\EmbedderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmbedders extends ListRecords
{
    protected static string $resource = EmbedderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
