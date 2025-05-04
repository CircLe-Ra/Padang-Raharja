<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AspirationResponse extends Model
{
    protected $fillable = [
        'aspiration_id',
        'responder_id',
        'response',
    ];

    public function aspiration(): BelongsTo
    {
        return $this->belongsTo(Aspiration::class);
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responder_id');
    }
}
