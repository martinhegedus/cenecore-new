<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Filament\Resources\Support\HandlesTranslatedRecords;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    use HandlesTranslatedRecords {
        mutateFormDataBeforeSave as mutateTranslatedFormDataBeforeSave;
    }

    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $this->mutateTranslatedFormDataBeforeSave($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
