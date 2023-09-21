<?php

namespace App\Filament\Resources\SplitterResource\Pages;

use App\Filament\Resources\SplitterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSplitter extends CreateRecord
{
    protected static string $resource = SplitterResource::class;
    protected static bool $canCreateAnother = false;

}
