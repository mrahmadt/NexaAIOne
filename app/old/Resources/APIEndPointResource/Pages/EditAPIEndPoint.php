<?php

namespace App\Filament\Resources\APIEndPointResource\Pages;

use App\Filament\Resources\APIEndPointResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAPIEndPoint extends EditRecord
{
    protected static string $resource = APIEndPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
