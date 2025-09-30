<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Umkm extends Model
{
    protected $fillable = [
        'user_id',
        'nama_umkm',
        'deskripsi',
        'kategori',
        'alamat',
        'no_telepon',
        'email',
        'latitude',
        'longitude',
        'gambar',
        'status',
        'facebook',
        'instagram',
        'twitter',
        'whatsapp',
        'website'
    ];

    // Fields that should never be mass assignable for security
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(UmkmMenu::class);
    }

    /**
     * Scope to get only UMKM owned by the current user
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if UMKM is owned by the given user
     */
    public function isOwnedBy($userId): bool
    {
        return $this->user_id === $userId;
    }
}
