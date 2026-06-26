<?php

namespace App\Models;

use App\Models\Concerns\HasCmsMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class ProjectCategory extends Model implements HasMedia
{
    use HasCmsMedia;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function cmsMediaCollections(): array
    {
        return [
            'cover' => ['single' => true, 'mime_types' => ['image/jpeg', 'image/png', 'image/webp']],
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProjectCategoryTranslation::class);
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(ProjectCategoryTranslation::class)->where('locale', $locale);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
