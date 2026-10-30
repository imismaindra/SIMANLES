<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Services\JadwalService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class JadwalLogicTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test pembuatan jadwal sukses saat tidak ada bentrok waktu.
     */
    public function test_schedule_creation_succeeds_without_collision(): void
    {
        $cabang = Cabang::firstOrCreate(['kode' => 'CAB_JADWAL_1'], [
            'nama' => 'Cabang Jadwal 1',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        $guru = Guru::create([
            'nama' => 'Guru Jadwal Test',
            'email' => 'guru.jadwal.'.uniqid().'@test.com',
            'no_telp' => '081234567890',
            'jenjang' => 'SMP',
            'is_active' => true,
        ]);

        $service = app(JadwalService::class);

        $jadwal = $service->createJadwal([
            'guru_id' => $guru->id,
            'cabang_id' => $cabang->id,
            'hari' => 'Senin',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
        ]);

        $this->assertInstanceOf(Jadwal::class, $jadwal);
        $this->assertSame('Senin', $jadwal->hari);
    }

    /**
     * Test deteksi bentrok jadwal mengajar lintas cabang:
     * Guru tidak boleh dijadwalkan pada waktu yang bertabrakan meskipun berada di cabang yang berbeda.
     */
    public function test_schedule_cross_branch_collision_is_prevented(): void
    {
        $cabangA = Cabang::create([
            'kode' => 'CJA_'.mt_rand(1000, 9999),
            'nama' => 'Cabang Yogyakarta Diponegoro',
            'alamat' => 'Diponegoro',
            'status' => 'aktif',
        ]);

        $cabangB = Cabang::create([
            'kode' => 'CJB_'.mt_rand(1000, 9999),
            'nama' => 'Cabang Sleman Kaliurang',
            'alamat' => 'Kaliurang',
            'status' => 'aktif',
        ]);

        $guru = Guru::create([
            'nama' => 'Guru Lintas Cabang',
            'email' => 'guru.lintas.'.uniqid().'@test.com',
            'no_telp' => '081234567890',
            'jenjang' => 'SMA_SMK',
            'is_active' => true,
        ]);

        $service = app(JadwalService::class);

        // 1. Jadwal awal di Cabang A: Senin 14:00 - 15:30
        $service->createJadwal([
            'guru_id' => $guru->id,
            'cabang_id' => $cabangA->id,
            'hari' => 'Senin',
            'jam_mulai' => '14:00',
            'jam_selesai' => '15:30',
        ]);

        // 2. Coba buat jadwal di Cabang B pada hari yang sama jam 15:00 - 16:30 (Irisan bentrok 15:00 - 15:30)
        $this->expectException(ValidationException::class);

        $service->createJadwal([
            'guru_id' => $guru->id,
            'cabang_id' => $cabangB->id,
            'hari' => 'Senin',
            'jam_mulai' => '15:00',
            'jam_selesai' => '16:30',
        ]);
    }
}
