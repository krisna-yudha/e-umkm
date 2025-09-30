<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UmkmMenu extends Model
{
    protected $fillable = [
        'umkm_id',
        'nama_menu',
        'deskripsi',
        'harga',
        'kategori_menu',
        'gambar_menu',
        'tersedia'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'tersedia' => 'boolean'
    ];

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }
}
