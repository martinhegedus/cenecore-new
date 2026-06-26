<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Filament\Resources\Support\HandlesTranslatedRecords;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    use HandlesTranslatedRecords {
        mutateFormDataBeforeCreate as mutateTranslatedFormDataBeforeCreate;
    }

    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $this->mutateTranslatedFormDataBeforeCreate($data);
    }
}
