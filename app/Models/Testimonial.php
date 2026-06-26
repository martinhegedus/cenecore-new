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
use InvalidArgumentException;
use Spatie\MediaLibrary\HasMedia;

class Testimonial extends Model implements HasMedia
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
            'rating' => 'integer',
        ];
    }

    public static function cmsMediaCollections(): array
    {
        return [
            'avatar' => ['single' => true, 'mime_types' => ['image/jpeg', 'image/png', 'image/webp']],
        ];
    }

    public function setRatingAttribute(?int $value): void
    {
        if ($value !== null && ($value < 1 || $value > 5)) {
            throw new InvalidArgumentException('The testimonial rating must be between 1 and 5.');
        }

        $this->attributes['rating'] = $value;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(TestimonialTranslation::class);
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(TestimonialTranslation::class)->where('locale', $locale);
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
