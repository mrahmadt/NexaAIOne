<?php

namespace App\Filament\Resources\EmbedderResource\Pages;

use App\Filament\Resources\EmbedderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmbedder extends CreateRecord
{
    protected static string $resource = EmbedderResource::class;
    protected static bool $canCreateAnother = false;

}
