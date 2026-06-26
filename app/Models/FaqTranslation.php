<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaqTranslation extends Model
{
    protected $guarded = [];

    public function faq(): BelongsTo
    {
        return $this->belongsTo(Faq::class);
    }
}
