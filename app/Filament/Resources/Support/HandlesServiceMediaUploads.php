<?php

namespace App\Filament\Resources\Support;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait HandlesServiceMediaUploads
{
    protected TemporaryUploadedFile|UploadedFile|null $pendingCoverUpload = null;

    protected TemporaryUploadedFile|UploadedFile|null $pendingOgUpload = null;

    protected function extractMediaUploads(array &$data): void
    {
        $this->pendingCoverUpload = $this->normalizeUpload($data['cover_upload'] ?? null);
        $this->pendingOgUpload = $this->normalizeUpload($data['og_upload'] ?? null);

        unset($data['cover_upload'], $data['og_upload']);
    }

    protected function attachPendingServiceMedia(Model $record): void
    {
        if (! $record instanceof Service) {
            return;
        }

        $this->attachSingleUpload($record, $this->pendingCoverUpload, 'cover');
        $this->attachSingleUpload($record, $this->pendingOgUpload, 'og');
    }

    protected function attachSingleUpload(Service $service, TemporaryUploadedFile|UploadedFile|null $file, string $collectionName): void
    {
        if (! $file) {
            return;
        }

        $service->clearMediaCollection($collectionName);

        $service
            ->addMedia($file)
            ->withCustomProperties([
                'alt' => [],
                'title' => null,
            ])
            ->toMediaCollection($collectionName);
    }

    protected function normalizeUpload(mixed $upload): TemporaryUploadedFile|UploadedFile|null
    {
        if ($upload instanceof TemporaryUploadedFile || $upload instanceof UploadedFile) {
            return $upload;
        }

        if (is_array($upload)) {
            return $this->normalizeUpload(collect($upload)->first());
        }

        return null;
    }
}
