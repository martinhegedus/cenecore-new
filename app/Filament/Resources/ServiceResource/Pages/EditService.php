<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\Support\HandlesServiceMediaUploads;
use App\Filament\Resources\Support\HandlesTranslatedRecords;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditService extends EditRecord
{
    use HandlesServiceMediaUploads;
    use HandlesTranslatedRecords {
        handleRecordUpdate as handleTranslatedRecordUpdate;
        mutateFormDataBeforeSave as mutateTranslatedFormDataBeforeSave;
    }

    protected static string $resource = ServiceResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->extractMediaUploads($data);

        return $this->mutateTranslatedFormDataBeforeSave($data);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = $this->handleTranslatedRecordUpdate($record, $data);

        $this->attachPendingServiceMedia($record);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
