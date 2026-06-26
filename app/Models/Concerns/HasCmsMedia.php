<?php

namespace App\Models\Concerns;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasCmsMedia
{
    use InteractsWithMedia;

    /**
     * @return array<string, array{single?: bool, mime_types?: array<int, string>}>
     */
    abstract public static function cmsMediaCollections(): array;

    public function registerMediaCollections(): void
    {
        foreach (static::cmsMediaCollections() as $name => $settings) {
            $collection = $this
                ->addMediaCollection($name)
                ->useDisk(config('media-library.disk_name', 'public'))
                ->storeConversionsOnDisk(config('media-library.conversions_disk_name') ?: config('media-library.disk_name', 'public'));

            if ($settings['single'] ?? false) {
                $collection->singleFile();
            }

            if ($mimeTypes = ($settings['mime_types'] ?? null)) {
                $collection->acceptsMimeTypes($mimeTypes);
            }

            if (static::supportsImageConversions() && static::collectionSupportsResponsiveImages($name)) {
                $collection->withResponsiveImages();
            }
        }
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if (! static::supportsImageConversions()) {
            return;
        }

        $imageCollections = static::imageCollectionNames();

        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 160, 160)
            ->quality(82)
            ->performOnCollections(...$imageCollections)
            ->nonQueued();

        $this->addMediaConversion('card')
            ->fit(Fit::Crop, 640, 420)
            ->quality(85)
            ->performOnCollections(...$imageCollections)
            ->nonQueued();

        $this->addMediaConversion('hero')
            ->fit(Fit::Crop, 1600, 700)
            ->quality(86)
            ->performOnCollections(...$imageCollections)
            ->nonQueued();

        $this->addMediaConversion('og')
            ->fit(Fit::Crop, 1200, 630)
            ->quality(86)
            ->performOnCollections(...$imageCollections)
            ->nonQueued();
    }

    public function preferredImageUrl(string $collectionName, string $conversion = ''): ?string
    {
        $url = $this->getFirstMediaUrl($collectionName, $conversion);

        return $url !== '' ? $url : null;
    }

    public function hasMediaInCollection(string $collectionName): bool
    {
        return $this->hasMedia($collectionName);
    }

    public function localizedMediaAlt(string $collectionName, string $locale, string $fallbackLocale = 'sk'): ?string
    {
        $media = $this->getFirstMedia($collectionName);

        if (! $media) {
            return null;
        }

        return $media->getCustomProperty("alt.{$locale}")
            ?? $media->getCustomProperty("alt.{$fallbackLocale}")
            ?? $media->getCustomProperty('alt');
    }

    /**
     * @return array<int, string>
     */
    protected static function imageCollectionNames(): array
    {
        return collect(static::cmsMediaCollections())
            ->reject(fn (array $settings): bool => in_array('application/pdf', $settings['mime_types'] ?? [], true))
            ->keys()
            ->values()
            ->all();
    }

    protected static function collectionSupportsResponsiveImages(string $collectionName): bool
    {
        return in_array($collectionName, ['hero', 'cover', 'content', 'gallery', 'og'], true);
    }

    protected static function supportsImageConversions(): bool
    {
        return match (config('media-library.image_driver', 'gd')) {
            'gd' => extension_loaded('gd'),
            'imagick' => extension_loaded('imagick'),
            'vips' => extension_loaded('vips'),
            default => false,
        };
    }
}
