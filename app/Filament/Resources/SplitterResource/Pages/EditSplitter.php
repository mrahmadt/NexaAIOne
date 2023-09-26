<?php

namespace App\Filament\Resources\SplitterResource\Pages;

use App\Filament\Resources\SplitterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSplitter extends EditRecord
{
    protected static string $resource = SplitterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['options'] = json_encode($data['options']);
    
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['options'] = json_decode($data['options']);
    
        return $data;
    }
}
