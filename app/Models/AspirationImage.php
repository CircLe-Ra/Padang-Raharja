<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AspirationImage extends Model
{
    protected $guarded = ['id'];

    public function aspiration(): BelongsTo
    {
        return $this->belongsTo(Aspiration::class);
    }
}
