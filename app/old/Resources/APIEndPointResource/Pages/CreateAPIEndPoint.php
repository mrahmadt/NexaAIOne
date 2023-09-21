<?php

namespace App\Filament\Resources\APIEndPointResource\Pages;

use App\Filament\Resources\APIEndPointResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAPIEndPoint extends CreateRecord
{
    protected static string $resource = APIEndPointResource::class;
    protected static bool $canCreateAnother = false;

}
