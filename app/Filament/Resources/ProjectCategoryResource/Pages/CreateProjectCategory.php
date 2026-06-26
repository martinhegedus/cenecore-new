<?php

namespace App\Filament\Resources\ProjectCategoryResource\Pages;

use App\Filament\Resources\ProjectCategoryResource;
use App\Filament\Resources\Support\HandlesTranslatedRecords;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectCategory extends CreateRecord
{
    use HandlesTranslatedRecords;

    protected static string $resource = ProjectCategoryResource::class;
}
