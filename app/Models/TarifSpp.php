<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifSpp extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tarif_spp';

    protected $fillable = [
        'jenjang',
        'kelas_min',
        'kelas_max',
        'nominal',
        'berlaku_sejak',
    ];

    protected function casts(): array
    {
        return [
            'kelas_min' => 'integer',
            'kelas_max' => 'integer',
            'nominal' => 'decimal:2',
            'berlaku_sejak' => 'date',
        ];
    }

    /**
     * Hitung tarif SPP berdasarkan jenjang dan kelas.
     * Menggunakan tarif di DB jika ada, atau fallback default standar.
     */
    public static function getRate(string $jenjang, int $kelas): float
    {
        $rate = static::where('jenjang', $jenjang)
            ->where('kelas_min', '<=', $kelas)
            ->where('kelas_max', '>=', $kelas)
            ->value('nominal');

        if ($rate !== null) {
            return (float) $rate;
        }

        return match ($jenjang) {
            'TK' => 150000.0,
            'SD' => $kelas <= 3 ? 150000.0 : 175000.0,
            'SMP' => 200000.0,
            default => 225000.0,
        };
    }
}
