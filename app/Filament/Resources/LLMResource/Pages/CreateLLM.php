<?php

namespace App\Filament\Resources\LlmResource\Pages;

use App\Filament\Resources\LlmResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLlm extends CreateRecord
{
    protected static string $resource = LlmResource::class;
    protected static bool $canCreateAnother = false;

}
