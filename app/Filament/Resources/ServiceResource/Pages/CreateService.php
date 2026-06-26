<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\Support\HandlesTranslatedRecords;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    use HandlesTranslatedRecords;

    protected static string $resource = ServiceResource::class;
}
