<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'umkm_id',
        'user_id',
        'rating',
        'review',
        'helpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'helpful_count' => 'integer',
    ];

    /**
     * Get the UMKM this rating belongs to
     */
    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }

    /**
     * Get the user who created this rating
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
