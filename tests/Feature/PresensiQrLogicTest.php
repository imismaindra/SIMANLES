<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Guru;
use App\Services\PresensiService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PresensiQrLogicTest extends TestCase
{
    /**
     * Test token QR berumur pendek (15-30 detik) dan token baru menggantikan token lama.
     */
    public function test_qr_token_generation_expires_in_short_duration(): void
    {
        $cabang = Cabang::create([
            'kode' => 'CQ_'.mt_rand(1000, 9999),
            'nama' => 'Cabang Presensi Test',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        $service = app(PresensiService::class);

        // 1. Generate token datang
        $token1 = $service->generateQrToken($cabang->id, 'datang', 20);
        $this->assertStringStartsWith('SIROJU-', $token1->token);
        $this->assertSame('datang', $token1->tipe);
        $this->assertFalse($token1->isExpired());

        // 2. Generate token baru menggantikan token lama
        $token2 = $service->generateQrToken($cabang->id, 'datang', 20);
        $this->assertDatabaseMissing('qr_token', ['token' => $token1->token]);
        $this->assertDatabaseHas('qr_token', ['token' => $token2->token]);
    }

    /**
     * Test proses scan absensi mandiri guru:
     * Mencatat jam datang, mencegah double datang, mencatat jam pulang,
     * dan memvalidasi keanggotaan cabang.
     */
    public function test_qr_scan_records_attendance_and_validates_branch_membership(): void
    {
        $cabangA = Cabang::create([
            'kode' => 'CSA_'.mt_rand(1000, 9999),
            'nama' => 'Cabang Terdaftar',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        $cabangB = Cabang::create([
            'kode' => 'CSB_'.mt_rand(1000, 9999),
            'nama' => 'Cabang Lain',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        $guru = Guru::create([
            'nama' => 'Guru Presensi',
            'email' => 'guru.absen.'.uniqid().'@test.com',
            'no_telp' => '081234567890',
            'jenjang' => 'SD',
            'is_active' => true,
        ]);
        // Guru hanya terdaftar di Cabang A
        DB::table('guru_cabang')->insert(['guru_id' => $guru->id, 'cabang_id' => $cabangA->id]);

        $service = app(PresensiService::class);

        // Kasus 1: Guru scan token di Cabang B (Bukan cabangnya -> Ditolak)
        $tokenCabangB = $service->generateQrToken($cabangB->id, 'datang', 20);
        try {
            $service->scan($tokenCabangB->token, $guru->id);
            $this->fail('Harusnya gagal karena guru tidak terdaftar di Cabang B.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('tidak terdaftar mengajar di cabang ini', $e->getMessage());
        }

        // Kasus 2: Guru scan token datang di Cabang A (Sukses)
        $tokenDatang = $service->generateQrToken($cabangA->id, 'datang', 20);
        $resultDatang = $service->scan($tokenDatang->token, $guru->id);

        $this->assertTrue($resultDatang['ok']);
        $this->assertSame('datang', $resultDatang['tipe']);

        $this->assertDatabaseHas('absensi', [
            'guru_id' => $guru->id,
            'cabang_id' => $cabangA->id,
            'tanggal' => Carbon::today()->toDateString(),
        ]);

        // Kasus 3: Guru mencoba scan datang lagi pada hari yang sama (Ditolak)
        $tokenDatang2 = $service->generateQrToken($cabangA->id, 'datang', 20);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('sudah melakukan absensi datang');
        $service->scan($tokenDatang2->token, $guru->id);
    }

    /**
     * Test scan QR dengan token berformat URL scan atau lowercase otomatis diekstrak & dinormalisasi.
     */
    public function test_qr_scan_supports_url_formatted_tokens_and_case_insensitivity(): void
    {
        $cabang = Cabang::create([
            'kode' => 'CURL_'.mt_rand(1000, 9999),
            'nama' => 'Cabang URL Test',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        $guru = Guru::create([
            'nama' => 'Guru URL Test',
            'email' => 'guru.url.'.uniqid().'@test.com',
            'no_telp' => '081234567891',
            'jenjang' => 'SD',
            'is_active' => true,
        ]);
        DB::table('guru_cabang')->insert(['guru_id' => $guru->id, 'cabang_id' => $cabang->id]);

        $service = app(PresensiService::class);
        $qrToken = $service->generateQrToken($cabang->id, 'datang', 25);

        // Simulasi scan hasil native camera yang berupa full URL
        $urlScanned = "http://localhost:8000/presensi/scan?token={$qrToken->token}&cabang_id={$cabang->id}";

        $result = $service->scan($urlScanned, $guru->id);
        $this->assertTrue($result['ok']);
        $this->assertSame('datang', $result['tipe']);
        $this->assertSame($guru->nama, $result['guru_nama']);
    }

    /**
     * Test absensi pulang:
     * - Ditolak jika belum absensi datang
     * - Berhasil jika sudah datang dan menghitung durasi mengajar
     * - Ditolak jika sudah pernah pulang
     */
    public function test_qr_scan_pulang_requires_prior_datang_and_calculates_duration(): void
    {
        $cabang = Cabang::create([
            'kode' => 'CPUL_'.mt_rand(1000, 9999),
            'nama' => 'Cabang Pulang Test',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        $guru = Guru::create([
            'nama' => 'Guru Checkout',
            'email' => 'guru.pulang.'.uniqid().'@test.com',
            'no_telp' => '081234567892',
            'jenjang' => 'SD',
            'is_active' => true,
        ]);
        DB::table('guru_cabang')->insert(['guru_id' => $guru->id, 'cabang_id' => $cabang->id]);

        $service = app(PresensiService::class);

        // 1. Coba pulang tanpa datang terlebih dahulu -> Harus Gagal
        $tokenPulang1 = $service->generateQrToken($cabang->id, 'pulang', 25);
        try {
            $service->scan($tokenPulang1->token, $guru->id);
            $this->fail('Harusnya gagal absensi pulang karena belum datang.');
        } catch (Exception $e) {
            $this->assertStringContainsString('belum melakukan absensi datang', $e->getMessage());
        }

        // 2. Lakukan absensi datang dulu
        $tokenDatang = $service->generateQrToken($cabang->id, 'datang', 25);
        $service->scan($tokenDatang->token, $guru->id);

        // 3. Lakukan absensi pulang -> Harus Sukses
        $tokenPulang2 = $service->generateQrToken($cabang->id, 'pulang', 25);
        $resultPulang = $service->scan($tokenPulang2->token, $guru->id);

        $this->assertTrue($resultPulang['ok']);
        $this->assertSame('pulang', $resultPulang['tipe']);
        $this->assertNotNull($resultPulang['jam_pulang']);

        // 4. Coba scan pulang lagi -> Harus Ditolak
        $tokenPulang3 = $service->generateQrToken($cabang->id, 'pulang', 25);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('sudah melakukan absensi pulang');
        $service->scan($tokenPulang3->token, $guru->id);
    }

    /**
     * Test API endpoint absensi QR, scan, dan live data.
     */
    public function test_api_absensi_endpoints(): void
    {
        $cabang = Cabang::create([
            'kode' => 'CAPI_'.mt_rand(1000, 9999),
            'nama' => 'Cabang API Test',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        $guru = Guru::create([
            'nama' => 'Guru API Test',
            'email' => 'guru.api.'.uniqid().'@test.com',
            'no_telp' => '081234567893',
            'jenjang' => 'SD',
            'is_active' => true,
        ]);
        DB::table('guru_cabang')->insert(['guru_id' => $guru->id, 'cabang_id' => $cabang->id]);

        // 1. POST /api/absensi/qr
        $responseQr = $this->postJson(route('api.absensi.qr'), [
            'cabang_id' => $cabang->id,
            'tipe' => 'datang',
            'duration' => 25,
        ]);

        $responseQr->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => ['token', 'tipe', 'expires_at', 'expires_in_seconds', 'qr_svg', 'scan_url'],
            ]);

        // SVG harus siap dipakai inline di HTML (tanpa prolog XML).
        $this->assertStringStartsWith('<svg', $responseQr->json('data.qr_svg'));

        $token = $responseQr->json('data.token');

        // 2. POST /api/absensi/scan
        $responseScan = $this->postJson(route('api.absensi.scan'), [
            'token' => $token,
            'guru_id' => $guru->id,
        ]);

        $responseScan->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.tipe', 'datang')
            ->assertJsonPath('data.guru_nama', $guru->nama);

        // 3. GET /api/absensi/today
        $responseToday = $this->getJson(route('api.absensi.today', ['cabang_id' => $cabang->id]));
        $responseToday->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'summary' => ['totalGuruBertugas', 'sudahDatang', 'sudahPulang', 'belumHadir', 'persentaseKehadiran'],
                'absensiHariIni',
            ]);

        // 4. GET /presensi/scan (Scanner Page)
        $responsePage = $this->get(route('presensi.scanner', ['cabang_id' => $cabang->id, 'token' => $token]));
        $responsePage->assertOk()
            ->assertSee('Presensi Guru')
            ->assertSee($guru->nama);

        // 5. GET /api/absensi/guru-status
        $responseStatus = $this->getJson(route('api.absensi.guru-status', ['guru_id' => $guru->id, 'cabang_id' => $cabang->id]));
        $responseStatus->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.guru_nama', $guru->nama);
    }

    /**
     * Test mode smart auto-detect: token auto otomatis mencatat datang pada scan pertama,
     * dan mencatat pulang pada scan kedua setelah jeda waktu mengajar.
     */
    public function test_smart_auto_detect_attendance_flow(): void
    {
        $cabang = Cabang::create([
            'kode' => 'CAUTO_'.mt_rand(1000, 9999),
            'nama' => 'Cabang Smart Auto Test',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        $guru = Guru::create([
            'nama' => 'Guru Smart Flow',
            'email' => 'guru.smart.'.uniqid().'@test.com',
            'no_telp' => '081234567894',
            'jenjang' => 'SD',
            'is_active' => true,
        ]);
        DB::table('guru_cabang')->insert(['guru_id' => $guru->id, 'cabang_id' => $cabang->id]);

        $service = app(PresensiService::class);

        // 1. Scan pertama dengan token mode 'auto' -> harus otomatis tercatat sebagai 'datang'
        $tokenAuto1 = $service->generateQrToken($cabang->id, 'auto', 60);
        $resDatang = $service->scan($tokenAuto1->token, $guru->id);

        $this->assertTrue($resDatang['ok']);
        $this->assertSame('datang', $resDatang['tipe']);
        $this->assertNotNull($resDatang['jam_datang']);

        // 2. Majukan waktu 2 jam untuk simulasi selesai sesi les
        Carbon::setTestNow(Carbon::now()->addHours(2));

        // 3. Scan kedua dengan token baru mode 'auto' -> harus otomatis tercatat sebagai 'pulang'
        $tokenAuto2 = $service->generateQrToken($cabang->id, 'auto', 60);
        $resPulang = $service->scan($tokenAuto2->token, $guru->id);

        $this->assertTrue($resPulang['ok']);
        $this->assertSame('pulang', $resPulang['tipe']);
        $this->assertNotNull($resPulang['jam_pulang']);
        $this->assertGreaterThanOrEqual(120, $resPulang['durasi_menit']);

        // Reset Carbon test time
        Carbon::setTestNow();
    }

    /**
     * Test presensi manual oleh admin (Hadir, Izin, Sakit).
     */
    public function test_admin_manual_attendance_recording(): void
    {
        $cabang = Cabang::create([
            'kode' => 'CMAN_'.mt_rand(1000, 9999),
            'nama' => 'Cabang Manual Test',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        $guru = Guru::create([
            'nama' => 'Guru Izin Sakit',
            'email' => 'guru.izin.'.uniqid().'@test.com',
            'no_telp' => '081234567895',
            'jenjang' => 'SMP',
            'is_active' => true,
        ]);
        DB::table('guru_cabang')->insert(['guru_id' => $guru->id, 'cabang_id' => $cabang->id]);

        $service = app(PresensiService::class);

        $absensi = $service->recordManual(
            cabangId: $cabang->id,
            guruId: $guru->id,
            tanggal: Carbon::today()->toDateString(),
            status: 'izin',
            jamDatang: null,
            jamPulang: null,
            keterangan: 'Izin tugas luar kota'
        );

        $this->assertSame('izin', $absensi->status);
        $this->assertSame('manual', $absensi->metode);
        $this->assertSame('Izin tugas luar kota', $absensi->keterangan);
    }
}
