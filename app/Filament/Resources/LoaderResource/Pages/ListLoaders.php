<?php

namespace App\Filament\Resources\LoaderResource\Pages;

use App\Filament\Resources\LoaderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoaders extends ListRecords
{
    protected static string $resource = LoaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
