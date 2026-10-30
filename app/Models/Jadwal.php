<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jadwal';

    protected $fillable = [
        'guru_id',
        'siswa_id',
        'cabang_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function scopeHari(Builder $query, string $hari): Builder
    {
        return $query->where('hari', $hari);
    }

    public function scopeGuru(Builder $query, string $guruId): Builder
    {
        return $query->where('guru_id', $guruId);
    }

    public function scopeCabang(Builder $query, string $cabangId): Builder
    {
        return $query->where('cabang_id', $cabangId);
    }
}
