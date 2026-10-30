<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\PembayaranSpp;
use App\Models\Siswa;
use App\Models\TarifSpp;
use App\Models\Transaksi;
use App\Services\SppService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SppAndKasLogicTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test otomasi tagihan SPP bulanan yang idempoten (Cron tanggal 1 00:05).
     * Jika cron dijalankan ulang dalam bulan yang sama, tidak menduplikasi baris tagihan.
     */
    public function test_monthly_spp_billing_command_is_idempotent(): void
    {
        $cabang = Cabang::create([
            'kode' => 'CSP_'.mt_rand(1000, 9999),
            'nama' => 'Cabang Test SPP',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        TarifSpp::updateOrCreate(
            ['jenjang' => 'SMP', 'kelas_min' => 7, 'kelas_max' => 9],
            ['nominal' => 200000.0, 'berlaku_sejak' => '2026-01-01']
        );

        $siswa = Siswa::create([
            'cabang_id' => $cabang->id,
            'nama' => 'Siswa Test SPP Idempoten',
            'tanggal_lahir' => '2012-01-01',
            'alamat' => 'Alamat',
            'nama_ibu' => 'Ibu',
            'no_telp_ortu' => '081234567890',
            'jenjang' => 'SMP',
            'kelas' => 8,
            'status' => 'aktif',
        ]);

        $bulan = 11;
        $tahun = 2026;

        $service = app(SppService::class);

        // Eksekusi pertama: Tagihan dibuat
        $result1 = $service->generateMonthlyBilling($bulan, $tahun, $cabang->id);
        $this->assertSame(1, $result1['created']);
        $this->assertSame(0, $result1['skipped']);

        $this->assertDatabaseHas('pembayaran_spp', [
            'siswa_id' => $siswa->id,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'nominal' => 200000.0,
            'status' => 'belum_bayar',
        ]);

        // Eksekusi kedua (idempoten): Tidak ada tagihan ganda yang dibuat
        $result2 = $service->generateMonthlyBilling($bulan, $tahun, $cabang->id);
        $this->assertSame(0, $result2['created']);
        $this->assertSame(1, $result2['skipped']);

        // Verifikasi via Artisan Command
        $this->artisan("spp:generate-monthly {$bulan} {$tahun} --cabang={$cabang->id}")
            ->assertSuccessful();
    }

    /**
     * Test pelunasan SPP menerbitkan nomor kwitansi resmi standar:
     * Format: KW/{KODE_CABANG}/{TAHUN}/{BULAN_2_DIGIT}/{NOMOR_URUT_4_DIGIT}
     * Serta mencatat pemasukan kas cabang secara otomatis dan menyediakan tautan WhatsApp.
     */
    public function test_spp_payment_generates_official_receipt_and_records_cash_transaction(): void
    {
        $cabang = Cabang::create([
            'kode' => 'CAB99',
            'nama' => 'Cabang Spesial Kwitansi',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        $siswa = Siswa::create([
            'cabang_id' => $cabang->id,
            'nama' => 'Nabila Putri Santoso',
            'tanggal_lahir' => '2015-01-01',
            'alamat' => 'Alamat',
            'nama_ibu' => 'Ibu Ratna',
            'no_telp_ortu' => '081234567890',
            'jenjang' => 'SD',
            'kelas' => 5,
            'status' => 'aktif',
        ]);

        $spp = PembayaranSpp::create([
            'siswa_id' => $siswa->id,
            'cabang_id' => $cabang->id,
            'bulan' => 10,
            'tahun' => 2026,
            'nominal' => 175000.0,
            'status' => 'belum_bayar',
        ]);

        $service = app(SppService::class);
        $paidSpp = $service->bayar($spp->id, [
            'nama_pembayar' => 'Bpk. Hendra Santoso',
            'tanggal_bayar' => '2026-10-30',
        ]);

        $this->assertSame('lunas', $paidSpp->status);
        $this->assertNotNull($paidSpp->no_kwitansi);

        // Verifikasi format standar: KW/KODE_CABANG/TAHUN/BULAN/URUT
        $this->assertMatchesRegularExpression('/^KW\/CAB99\/2026\/10\/\d{4}$/', $paidSpp->no_kwitansi);

        // Verifikasi pencatatan otomatis di transaksi kas cabang
        $this->assertDatabaseHas('transaksi', [
            'cabang_id' => $cabang->id,
            'tipe' => 'masuk',
            'kategori' => 'SPP',
            'nominal' => 175000.0,
            'referensi_id' => $spp->id,
        ]);

        // Verifikasi URL WhatsApp
        $waLink = $paidSpp->generateWhatsAppLink();
        $this->assertNotNull($waLink);
        $this->assertStringContainsString('wa.me/6281234567890', $waLink);
        $this->assertStringContainsString(urlencode($paidSpp->no_kwitansi), $waLink);
    }
}
