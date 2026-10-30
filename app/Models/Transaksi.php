<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'transaksi';

    protected $fillable = [
        'cabang_id',
        'tipe',
        'kategori',
        'nominal',
        'keterangan',
        'tanggal',
        'referensi_id',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function scopeMasuk(Builder $query): Builder
    {
        return $query->where('tipe', 'masuk');
    }

    public function scopeKeluar(Builder $query): Builder
    {
        return $query->where('tipe', 'keluar');
    }

    public function scopeCabang(Builder $query, string $cabangId): Builder
    {
        return $query->where('cabang_id', $cabangId);
    }
}

