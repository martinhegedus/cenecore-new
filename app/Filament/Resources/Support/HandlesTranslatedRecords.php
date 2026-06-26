<?php

namespace App\Filament\Resources\Support;

use Illuminate\Database\Eloquent\Model;

trait HandlesTranslatedRecords
{
    /**
     * @var array<string, array<string, mixed>>
     */
    protected array $pendingTranslations = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['translations'] = ResourceTranslations::formData($this->getRecord(), static::getResource()::translationFields());

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $resource = static::getResource();

        $this->pendingTranslations = ResourceTranslations::extract($data, $resource::translationFields());

        ResourceTranslations::validate(
            record: null,
            translations: $this->pendingTranslations,
            translationTable: $resource::translationTable(),
            foreignKey: $resource::translationForeignKey(),
            hasSlug: static::hasTranslatedSlug($resource),
            requiresSlovakTranslation: static::requiresSlovakTranslation($data),
        );

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $resource = static::getResource();

        $this->pendingTranslations = ResourceTranslations::extract($data, $resource::translationFields());

        ResourceTranslations::validate(
            record: $this->getRecord(),
            translations: $this->pendingTranslations,
            translationTable: $resource::translationTable(),
            foreignKey: $resource::translationForeignKey(),
            hasSlug: static::hasTranslatedSlug($resource),
            requiresSlovakTranslation: static::requiresSlovakTranslation($data),
        );

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation($data);

        ResourceTranslations::save($record, $this->pendingTranslations, static::getResource()::translationFields());

        return $record;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);

        ResourceTranslations::save($record, $this->pendingTranslations, static::getResource()::translationFields());

        return $record;
    }

    protected static function requiresSlovakTranslation(array $data): bool
    {
        return ($data['status'] ?? null) === 'published';
    }

    protected static function hasTranslatedSlug(string $resource): bool
    {
        return in_array('slug', $resource::translationFields(), true);
    }
}
