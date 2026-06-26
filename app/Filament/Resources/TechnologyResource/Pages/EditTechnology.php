<?php

namespace App\Filament\Resources\TechnologyResource\Pages;

use App\Filament\Resources\Support\HandlesTranslatedRecords;
use App\Filament\Resources\TechnologyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTechnology extends EditRecord
{
    use HandlesTranslatedRecords;

    protected static string $resource = TechnologyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
