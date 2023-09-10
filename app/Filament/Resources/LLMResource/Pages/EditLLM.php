<?php

namespace App\Filament\Resources\LLMResource\Pages;

use App\Filament\Resources\LLMResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLLM extends EditRecord
{
    protected static string $resource = LLMResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
