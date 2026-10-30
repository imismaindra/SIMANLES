<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'siswa';

    protected $fillable = [
        'cabang_id',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'nama_ibu',
        'no_telp_ortu',
        'jenjang',
        'kelas',
        'guru_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'kelas' => 'integer',
        ];
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function pembayaranSpp(): HasMany
    {
        return $this->hasMany(PembayaranSpp::class, 'siswa_id');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'siswa_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function scopeTanpaGuru(Builder $query): Builder
    {
        return $query->whereNull('guru_id');
    }

    public function scopeCabang(Builder $query, string $cabangId): Builder
    {
        return $query->where('cabang_id', $cabangId);
    }
}
