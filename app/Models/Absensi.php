<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'absensi';

    protected $fillable = [
        'guru_id',
        'user_id',
        'cabang_id',
        'tanggal',
        'jam_datang',
        'jam_pulang',
        'status',
        'keterangan',
        'metode',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jam_datang' => 'datetime',
            'jam_pulang' => 'datetime',
        ];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }
}
