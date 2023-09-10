<?php

namespace App\Filament\Resources\LLMResource\Pages;

use App\Filament\Resources\LLMResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLLMs extends ListRecords
{
    protected static string $resource = LLMResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
