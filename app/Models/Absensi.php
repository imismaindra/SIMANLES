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
        'status_validasi',
        'is_manual_override',
        'alasan_validasi',
        'keterangan',
        'metode',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jam_datang' => 'datetime',
            'jam_pulang' => 'datetime',
            'is_manual_override' => 'boolean',
        ];
    }

    /**
     * Hitung status validitas kehadiran guru (Jadwal 18:00 - 20:00 WIB).
     * Check-in wajib sebelum/pada 18:00 WIB dan Check-out setelah/pada 20:00 WIB.
     */
    public function getValiditasAttribute(): string
    {
        if ($this->is_manual_override && ! empty($this->status_validasi)) {
            return $this->status_validasi;
        }

        if (! $this->jam_datang) {
            return 'tidak_valid';
        }

        $jamDatang = $this->jam_datang->format('H:i:s');

        // Jika belum check-out
        if (! $this->jam_pulang) {
            if ($jamDatang > '18:00:59') {
                return 'tidak_valid';
            }

            return 'sedang_mengajar';
        }

        $jamPulang = $this->jam_pulang->format('H:i:s');

        // Check-in <= 18:00 dan Check-out >= 20:00
        $checkInOnTime = $jamDatang <= '18:00:59';
        $checkOutOnTime = $jamPulang >= '20:00:00';

        return ($checkInOnTime && $checkOutOnTime) ? 'valid' : 'tidak_valid';
    }

    /**
     * Keterangan detail alasan valid / tidak valid.
     */
    public function getKeteranganValiditasAttribute(): string
    {
        if ($this->is_manual_override && ! empty($this->alasan_validasi)) {
            return $this->alasan_validasi.' (Manual)';
        }

        if (! $this->jam_datang) {
            return 'Belum melakukan check-in';
        }

        $jamDatang = $this->jam_datang->format('H:i');

        if (! $this->jam_pulang) {
            if ($this->jam_datang->format('H:i:s') > '18:00:59') {
                return "Terlambat check-in ({$jamDatang} WIB > 18:00)";
            }

            return 'Sedang mengajar (menunggu checkout setelah 20:00)';
        }

        $jamPulang = $this->jam_pulang->format('H:i');
        $checkInLate = $this->jam_datang->format('H:i:s') > '18:00:59';
        $checkOutEarly = $this->jam_pulang->format('H:i:s') < '20:00:00';

        if ($checkInLate && $checkOutEarly) {
            return "Terlambat ({$jamDatang}) & Pulang awal ({$jamPulang})";
        } elseif ($checkOutEarly) {
            return "Checkout sebelum jam 20:00 ({$jamPulang} WIB)";
        } elseif ($checkInLate) {
            return "Check-in setelah jam 18:00 ({$jamDatang} WIB)";
        }

        return 'Check-in & checkout tepat waktu';
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
