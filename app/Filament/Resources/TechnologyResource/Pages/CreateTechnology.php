<?php

namespace App\Filament\Resources\TechnologyResource\Pages;

use App\Filament\Resources\Support\HandlesTranslatedRecords;
use App\Filament\Resources\TechnologyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTechnology extends CreateRecord
{
    use HandlesTranslatedRecords;

    protected static string $resource = TechnologyResource::class;
}
