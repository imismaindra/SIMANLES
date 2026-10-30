<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\PembayaranSpp;
use App\Models\Siswa;
use App\Models\Transaksi;
use App\Services\SiswaService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SiswaLogicTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test pendaftaran siswa baru menghitung diskon secara akurat,
     * mencatat jurnal transaksi kas masuk cabang, dan menerbitkan tagihan SPP bulan berjalan.
     */
    public function test_siswa_registration_calculates_discount_and_creates_spp_and_kas_transaction(): void
    {
        $cabang = Cabang::firstOrCreate(['kode' => 'TEST_CAB'], [
            'nama' => 'Cabang Testing',
            'alamat' => 'Alamat Testing',
            'status' => 'aktif',
        ]);

        $payload = [
            'cabang_id' => $cabang->id,
            'nama' => 'Bintang Pratama Testing',
            'kelas' => 5,
            'jenjang' => 'SD',
            'tanggal_lahir' => '2015-08-10',
            'nama_ibu' => 'Ibu Endang',
            'no_telp_ortu' => '081234567899',
            'diskon' => 25000, // Biaya standar 100.000 - 25.000 = 75.000
        ];

        $response = $this->postJson('/api/siswa', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $siswaId = $response->json('data.id');

        // 1. Verifikasi data siswa
        $siswa = Siswa::find($siswaId);
        $this->assertNotNull($siswa);
        $this->assertSame('Bintang Pratama Testing', $siswa->nama);
        $this->assertSame('SD', $siswa->jenjang);
        $this->assertSame(5, $siswa->kelas);
        $this->assertSame('aktif', $siswa->status);

        // 2. Verifikasi pencatatan kas masuk pendaftaran (100.000 - 25.000 = 75.000)
        $this->assertDatabaseHas('transaksi', [
            'cabang_id' => $cabang->id,
            'tipe' => 'masuk',
            'kategori' => 'Pendaftaran',
            'nominal' => 75000.0,
            'referensi_id' => $siswaId,
        ]);

        // 3. Verifikasi tagihan SPP bulan berjalan otomatis diterbitkan
        $this->assertDatabaseHas('pembayaran_spp', [
            'siswa_id' => $siswaId,
            'cabang_id' => $cabang->id,
            'bulan' => now()->month,
            'tahun' => now()->year,
            'status' => 'belum_bayar',
        ]);
    }

    /**
     * Test kenaikan kelas massal akhir tahun ajaran:
     * Siswa kelas 1 s/d 11 naik 1 tingkat, siswa kelas 12 otomatis berstatus 'keluar' (lulus).
     */
    public function test_kenaikan_kelas_massal_promotes_grades_and_graduates_class_12(): void
    {
        $cabang = Cabang::firstOrCreate(['kode' => 'TEST_CAB_2'], [
            'nama' => 'Cabang Testing 2',
            'alamat' => 'Alamat Testing 2',
            'status' => 'aktif',
        ]);

        $siswaSD = Siswa::create([
            'cabang_id' => $cabang->id,
            'nama' => 'Siswa Naik Kelas SD',
            'tanggal_lahir' => '2016-01-01',
            'alamat' => 'Yogyakarta',
            'nama_ibu' => 'Ibu Ani',
            'no_telp_ortu' => '081234567891',
            'jenjang' => 'SD',
            'kelas' => 5,
            'status' => 'aktif',
        ]);

        $siswaSMA = Siswa::create([
            'cabang_id' => $cabang->id,
            'nama' => 'Siswa Lulus SMA',
            'tanggal_lahir' => '2008-01-01',
            'alamat' => 'Yogyakarta',
            'nama_ibu' => 'Ibu Budi',
            'no_telp_ortu' => '081234567892',
            'jenjang' => 'SMA_SMK',
            'kelas' => 12,
            'status' => 'aktif',
        ]);

        $service = app(SiswaService::class);
        $result = $service->kenaikanKelasMassal($cabang->id);

        $this->assertGreaterThanOrEqual(1, $result['promoted']);
        $this->assertGreaterThanOrEqual(1, $result['graduated']);

        // Siswa SD naik ke kelas 6
        $siswaSD->refresh();
        $this->assertSame(6, $siswaSD->kelas);
        $this->assertSame('aktif', $siswaSD->status);

        // Siswa SMA kelas 12 berstatus 'keluar'
        $siswaSMA->refresh();
        $this->assertSame('keluar', $siswaSMA->status);
        $this->assertNull($siswaSMA->guru_id);
    }

    /**
     * Test soft delete siswa mempertahankan riwayat transaksi & SPP masa lalu.
     */
    public function test_soft_delete_preserves_payment_and_transaction_history(): void
    {
        $cabang = Cabang::firstOrCreate(['kode' => 'TEST_CAB_3'], [
            'nama' => 'Cabang Testing 3',
            'alamat' => 'Alamat Testing 3',
            'status' => 'aktif',
        ]);

        $siswa = Siswa::create([
            'cabang_id' => $cabang->id,
            'nama' => 'Siswa Soft Delete',
            'tanggal_lahir' => '2015-01-01',
            'alamat' => 'Yogyakarta',
            'nama_ibu' => 'Ibu Siti',
            'no_telp_ortu' => '081234567893',
            'jenjang' => 'SD',
            'kelas' => 4,
            'status' => 'aktif',
        ]);

        $spp = PembayaranSpp::create([
            'siswa_id' => $siswa->id,
            'cabang_id' => $cabang->id,
            'bulan' => 9,
            'tahun' => 2026,
            'nominal' => 175000,
            'status' => 'lunas',
            'no_kwitansi' => 'KW/CAB01/2026/09/0001',
        ]);

        $transaksi = Transaksi::create([
            'cabang_id' => $cabang->id,
            'tipe' => 'masuk',
            'kategori' => 'SPP',
            'nominal' => 175000,
            'keterangan' => 'Pembayaran SPP Siswa Soft Delete',
            'tanggal' => '2026-09-10',
            'referensi_id' => $spp->id,
        ]);

        // Hapus siswa via API
        $response = $this->deleteJson("/api/siswa/{$siswa->id}");
        $response->assertStatus(200);

        // Siswa terhapus secara soft delete
        $this->assertSoftDeleted('siswa', ['id' => $siswa->id]);

        // Rekam jejak audit keuangan SPP & kas transaksi TETAP UTUH di database
        $this->assertDatabaseHas('pembayaran_spp', ['id' => $spp->id, 'status' => 'lunas']);
        $this->assertDatabaseHas('transaksi', ['id' => $transaksi->id, 'nominal' => 175000]);
    }
}
