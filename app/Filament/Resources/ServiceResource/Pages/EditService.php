<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\Support\HandlesTranslatedRecords;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    use HandlesTranslatedRecords;

    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
