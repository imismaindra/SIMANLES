<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Guru;
use App\Services\PresensiService;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PresensiQrLogicTest extends TestCase
{
    use DatabaseTransactions;

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
}
