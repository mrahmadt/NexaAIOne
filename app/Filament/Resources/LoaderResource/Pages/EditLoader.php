<?php

namespace App\Filament\Resources\LoaderResource\Pages;

use App\Filament\Resources\LoaderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoader extends EditRecord
{
    protected static string $resource = LoaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
