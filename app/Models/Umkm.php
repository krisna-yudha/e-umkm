<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'status'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
