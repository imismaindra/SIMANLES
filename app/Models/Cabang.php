<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cabang';

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'cabang_id');
    }

    public function guru(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'guru_cabang', 'cabang_id', 'guru_id');
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'cabang_id');
    }

    public function pembayaranSpp(): HasMany
    {
        return $this->hasMany(PembayaranSpp::class, 'cabang_id');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'cabang_id');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'cabang_id');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'cabang_id');
    }

    public function qrToken(): HasMany
    {
        return $this->hasMany(QrToken::class, 'cabang_id');
    }
}
