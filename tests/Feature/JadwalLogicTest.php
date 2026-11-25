<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\User;
use App\Services\JadwalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class JadwalLogicTest extends TestCase
{
    use RefreshDatabase;

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

    /**
     * Test guru dapat melihat siswa bimbingan pada halaman jadwal meskipun jadwal belum dibuat manual.
     */
    public function test_guru_can_view_assigned_students_on_jadwal_page_even_without_manual_schedule(): void
    {
        $cabang = Cabang::create([
            'kode' => 'CAB_JADWAL_'.mt_rand(1000, 9999),
            'nama' => 'Cabang Jadwal Siswa',
            'alamat' => 'Alamat Cabang',
            'status' => 'aktif',
        ]);

        $user = User::create([
            'name' => 'Guru Bimbingan Test',
            'email' => 'guru.bimbingan.'.uniqid().'@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'cabang_id' => $cabang->id,
            'is_active' => true,
        ]);

        $guru = Guru::create([
            'user_id' => $user->id,
            'nama' => 'Guru Bimbingan Test',
            'email' => $user->email,
            'no_telp' => '081234567890',
            'jenjang' => 'SD',
            'is_active' => true,
        ]);

        $siswa = Siswa::create([
            'cabang_id' => $cabang->id,
            'nama' => 'Siswa Bimbingan Unik',
            'tempat_lahir' => 'Sidoarjo',
            'tanggal_lahir' => '2015-01-01',
            'nama_ibu' => 'Ibu Siswa',
            'no_telp_ortu' => '081234567890',
            'alamat' => 'Alamat Siswa',
            'jenjang' => 'SD',
            'kelas' => 4,
            'guru_id' => $guru->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->get(route('jadwal.index'));

        $response->assertStatus(200);
        $response->assertSee('Siswa Bimbingan Unik');
        $response->assertSee('Guru Bimbingan Test');
        $response->assertSee('1/6 Siswa');
    }

    public function test_jadwal_page_displays_guru_profile_photo_when_logged_in_as_guru(): void
    {
        $cabang = Cabang::firstOrCreate(['kode' => 'CAB_JADWAL_PRF'], [
            'nama' => 'Cabang Jadwal Profil',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        $photoPath = 'profil/test_jadwal_guru.webp';

        $user = User::create([
            'name' => 'Bu Guru Jadwal Foto',
            'email' => 'guru.jadwal.foto@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'no_pegawai' => 'NIP-JADWAL-01',
            'cabang_id' => $cabang->id,
            'foto' => $photoPath,
            'is_active' => true,
        ]);

        $guru = Guru::create([
            'user_id' => $user->id,
            'nama' => 'Bu Guru Jadwal Foto',
            'email' => 'guru.jadwal.foto@test.com',
            'no_pegawai' => 'NIP-JADWAL-01',
            'no_telp' => '081234567890',
            'jenjang' => 'SMP',
            'foto' => $photoPath,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('jadwal.index'));

        $response->assertStatus(200);
        $response->assertSee('storage/'.$photoPath);
    }
}
