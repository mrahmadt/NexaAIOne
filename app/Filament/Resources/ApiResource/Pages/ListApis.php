<?php

namespace App\Filament\Resources\ApiResource\Pages;

use App\Filament\Resources\ApiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApis extends ListRecords
{
    protected static string $resource = ApiResource::class;
    public function getSubheading(): ?string
    {
        return __('Create an API and associate it with AI service and customize it to your needs. You can then use it in your app.');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
