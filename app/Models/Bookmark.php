<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    protected $fillable = [
        'user_id',
        'location_id',
    ];

    /**
     * Relasi ke user pemilik bookmark
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke lokasi yang di-bookmark
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
