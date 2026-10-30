<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrToken extends Model
{
    use HasFactory;

    protected $table = 'qr_token';

    protected $primaryKey = 'token';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'token',
        'cabang_id',
        'tipe',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
