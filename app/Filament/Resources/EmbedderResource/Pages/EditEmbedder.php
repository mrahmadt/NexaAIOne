<?php

namespace App\Filament\Resources\EmbedderResource\Pages;

use App\Filament\Resources\EmbedderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmbedder extends EditRecord
{
    protected static string $resource = EmbedderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
