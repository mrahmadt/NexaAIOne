<?php

namespace App\Filament\Resources\LlmResource\Pages;

use App\Filament\Resources\LlmResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLlm extends EditRecord
{
    protected static string $resource = LlmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
