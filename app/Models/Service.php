<?php

namespace App\Models;

use App\Models\Concerns\HasCmsMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Service extends Model implements HasMedia
{
    use HasCmsMedia;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public static function cmsMediaCollections(): array
    {
        return [
            'cover' => ['single' => true, 'mime_types' => ['image/jpeg', 'image/png', 'image/webp']],
            'content' => ['mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']],
            'og' => ['single' => true, 'mime_types' => ['image/jpeg', 'image/png', 'image/webp']],
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceTranslation::class);
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(ServiceTranslation::class)->where('locale', $locale);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
