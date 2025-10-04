<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'reason',
        'status',
        'admin_id',
        'admin_note',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function generateCode(): string
    {
        $this->code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->save();
        return $this->code;
    }

    public function approve(int $adminId, ?string $note = null): void
    {
        $this->update([
            'status' => 'approved',
            'admin_id' => $adminId,
            'admin_note' => $note,
            'approved_at' => now(),
        ]);
        
        // Generate verification code when approved
        $this->generateCode();
    }

    public function reject(int $adminId, ?string $note = null): void
    {
        $this->update([
            'status' => 'rejected',
            'admin_id' => $adminId,
            'admin_note' => $note,
        ]);
    }
}