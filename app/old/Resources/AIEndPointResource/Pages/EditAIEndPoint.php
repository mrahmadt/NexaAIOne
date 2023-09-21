<?php

namespace App\Filament\Resources\AIEndPointResource\Pages;

use App\Filament\Resources\AIEndPointResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAIEndPoint extends EditRecord
{
    protected static string $resource = AIEndPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
