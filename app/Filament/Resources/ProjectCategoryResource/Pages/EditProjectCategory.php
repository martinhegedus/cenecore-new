<?php

namespace App\Filament\Resources\ProjectCategoryResource\Pages;

use App\Filament\Resources\ProjectCategoryResource;
use App\Filament\Resources\Support\HandlesTranslatedRecords;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectCategory extends EditRecord
{
    use HandlesTranslatedRecords;

    protected static string $resource = ProjectCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
