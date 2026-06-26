<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnologyTranslation extends Model
{
    protected $guarded = [];

    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
    }
}
