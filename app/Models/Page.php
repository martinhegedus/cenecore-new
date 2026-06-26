<?php

namespace App\Models;

use App\Models\Concerns\HasCmsMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Page extends Model implements HasMedia
{
    use HasCmsMedia;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_indexable' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public static function cmsMediaCollections(): array
    {
        return [
            'hero' => ['single' => true, 'mime_types' => ['image/jpeg', 'image/png', 'image/webp']],
            'content' => ['mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']],
            'og' => ['single' => true, 'mime_types' => ['image/jpeg', 'image/png', 'image/webp']],
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(PageTranslation::class)->where('locale', $locale);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
