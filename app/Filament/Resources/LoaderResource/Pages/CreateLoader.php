<?php

namespace App\Filament\Resources\LoaderResource\Pages;

use App\Filament\Resources\LoaderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLoader extends CreateRecord
{
    protected static string $resource = LoaderResource::class;
    protected static bool $canCreateAnother = false;

}
