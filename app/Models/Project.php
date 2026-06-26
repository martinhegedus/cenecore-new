<?php

namespace App\Models;

use App\Models\Concerns\HasCmsMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Project extends Model implements HasMedia
{
    use HasCmsMedia;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'completed_at' => 'date',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'started_at' => 'date',
        ];
    }

    public static function cmsMediaCollections(): array
    {
        return [
            'cover' => ['single' => true, 'mime_types' => ['image/jpeg', 'image/png', 'image/webp']],
            'gallery' => ['mime_types' => ['image/jpeg', 'image/png', 'image/webp']],
            'og' => ['single' => true, 'mime_types' => ['image/jpeg', 'image/png', 'image/webp']],
            'documents' => ['mime_types' => ['application/pdf']],
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProjectTranslation::class);
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(ProjectTranslation::class)->where('locale', $locale);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)->withTimestamps();
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)->withTimestamps();
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
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
