<?php

namespace App\Filament\Resources\ChunkResource\Pages;

use App\Filament\Resources\ChunkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChunk extends EditRecord
{
    protected static string $resource = ChunkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
