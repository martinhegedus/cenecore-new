<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\Support\HandlesServiceMediaUploads;
use App\Filament\Resources\Support\HandlesTranslatedRecords;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateService extends CreateRecord
{
    use HandlesServiceMediaUploads;
    use HandlesTranslatedRecords {
        handleRecordCreation as handleTranslatedRecordCreation;
        mutateFormDataBeforeCreate as mutateTranslatedFormDataBeforeCreate;
    }

    protected static string $resource = ServiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->extractMediaUploads($data);

        return $this->mutateTranslatedFormDataBeforeCreate($data);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = $this->handleTranslatedRecordCreation($data);

        $this->attachPendingServiceMedia($record);

        return $record;
    }
}
