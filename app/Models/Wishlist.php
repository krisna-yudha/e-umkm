<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    protected $fillable = [
        'umkm_id',
        'user_id',
    ];

    /**
     * Get the UMKM associated with this wishlist entry
     */
    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }

    /**
     * Get the user who wishlisted this UMKM
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
