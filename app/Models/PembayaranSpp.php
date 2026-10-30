<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranSpp extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pembayaran_spp';

    protected $fillable = [
        'siswa_id',
        'cabang_id',
        'bulan',
        'tahun',
        'nominal',
        'status',
        'nama_pembayar',
        'tanggal_bayar',
        'no_kwitansi',
        'kwitansi_url',
        'dikirim_at',
    ];

    protected function casts(): array
    {
        return [
            'bulan' => 'integer',
            'tahun' => 'integer',
            'nominal' => 'decimal:2',
            'tanggal_bayar' => 'date',
            'dikirim_at' => 'datetime',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function scopeBelumBayar(Builder $query): Builder
    {
        return $query->where('status', 'belum_bayar');
    }

    public function scopeLunas(Builder $query): Builder
    {
        return $query->where('status', 'lunas');
    }

    /**
     * Nama bulan dalam bahasa Indonesia.
     */
    public function getNamaBulanAttribute(): string
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $bulanNames[$this->bulan] ?? 'Bulan '.$this->bulan;
    }

    /**
     * Generate URL WhatsApp untuk notifikasi kwitansi resmi atau tagihan.
     */
    public function generateWhatsAppLink(): ?string
    {
        if (! $this->siswa || ! $this->siswa->no_telp_ortu) {
            return null;
        }

        // Normalisasi 08xxx -> 628xxx
        $phone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $this->siswa->no_telp_ortu));
        $namaSiswa = $this->siswa->nama;
        $formattedNominal = 'Rp '.number_format((float) $this->nominal, 0, ',', '.');

        if ($this->status === 'lunas') {
            $text = "Halo Orang Tua/Wali {$namaSiswa},\n\nTerima kasih telah melunasi SPP LKP Sang Siroju Lillah bulan {$this->nama_bulan} {$this->tahun}.\nNomor Kwitansi Resmi: {$this->no_kwitansi}\nNominal: {$formattedNominal}\n\nSemoga ananda terus berprestasi!";
        } else {
            $text = "Halo Orang Tua/Wali {$namaSiswa},\n\nPengingat pembayaran SPP LKP Sang Siroju Lillah bulan {$this->nama_bulan} {$this->tahun} sebesar {$formattedNominal}.\nMohon konfirmasi jika telah melakukan pembayaran. Terima kasih.";
        }

        return 'https://wa.me/'.$phone.'?text='.urlencode($text);
    }
}
