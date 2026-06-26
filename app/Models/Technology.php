<?php

namespace App\Models;

use App\Models\Concerns\HasCmsMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Technology extends Model implements HasMedia
{
    use HasCmsMedia;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public static function cmsMediaCollections(): array
    {
        return [
            'icon' => ['single' => true, 'mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']],
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(TechnologyTranslation::class);
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(TechnologyTranslation::class)->where('locale', $locale);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
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
