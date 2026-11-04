<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\Siswa;
use App\Services\GuruService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GuruAssignmentLogicTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test penolakan penugasan jika kualifikasi jenjang guru tidak cocok dengan jenjang siswa.
     */
    public function test_guru_assignment_rejects_incompatible_jenjang(): void
    {
        $cabang = Cabang::firstOrCreate(['kode' => 'CAB_ASSIGN_1'], [
            'nama' => 'Cabang Assignment 1',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        // Guru hanya berkualifikasi TK dan SD
        $guruSD = Guru::create([
            'nama' => 'Guru Khusus SD',
            'email' => 'guru.sd.'.uniqid().'@test.com',
            'no_telp' => '081234567890',
            'jenjang' => 'SD',
            'is_active' => true,
        ]);
        DB::table('guru_jenjang')->insert([
            ['guru_id' => $guruSD->id, 'jenjang' => 'TK'],
            ['guru_id' => $guruSD->id, 'jenjang' => 'SD'],
        ]);

        // Siswa berjenjang SMA_SMK
        $siswaSMA = Siswa::create([
            'cabang_id' => $cabang->id,
            'nama' => 'Siswa SMA Harapan',
            'tanggal_lahir' => '2009-01-01',
            'alamat' => 'Alamat',
            'nama_ibu' => 'Ibu Maria',
            'no_telp_ortu' => '081234567891',
            'jenjang' => 'SMA_SMK',
            'kelas' => 11,
            'status' => 'aktif',
        ]);

        $service = app(GuruService::class);

        $this->expectException(ValidationException::class);
        $service->assignSiswa($guruSD->id, $siswaSMA->id);
    }

    /**
     * Test aturan invarian kritis: Kuota guru maksimal 6 siswa aktif (Proteksi Race Condition).
     */
    public function test_guru_capacity_strictly_limited_to_max_6_active_students(): void
    {
        $cabang = Cabang::firstOrCreate(['kode' => 'CAB_ASSIGN_2'], [
            'nama' => 'Cabang Assignment 2',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        $guru = Guru::create([
            'nama' => 'Guru Populer',
            'email' => 'guru.populer.'.uniqid().'@test.com',
            'no_telp' => '081234567892',
            'jenjang' => 'SMP',
            'is_active' => true,
        ]);
        DB::table('guru_jenjang')->insert([
            ['guru_id' => $guru->id, 'jenjang' => 'SMP'],
        ]);

        // Berikan 6 siswa aktif penuh ke guru ini
        for ($i = 1; $i <= 6; $i++) {
            Siswa::create([
                'cabang_id' => $cabang->id,
                'nama' => "Siswa Bimbingan {$i}",
                'tanggal_lahir' => '2012-01-01',
                'alamat' => 'Alamat',
                'nama_ibu' => 'Ibu',
                'no_telp_ortu' => '081234567890',
                'jenjang' => 'SMP',
                'kelas' => 8,
                'guru_id' => $guru->id,
                'status' => 'aktif',
            ]);
        }

        $this->assertSame(6, $guru->active_students_count);

        // Siswa ke-7 yang mencoba masuk
        $siswaKe7 = Siswa::create([
            'cabang_id' => $cabang->id,
            'nama' => 'Siswa Ke-7 Melebihi Kuota',
            'tanggal_lahir' => '2012-05-01',
            'alamat' => 'Alamat',
            'nama_ibu' => 'Ibu 7',
            'no_telp_ortu' => '081234567897',
            'jenjang' => 'SMP',
            'kelas' => 8,
            'status' => 'aktif',
        ]);

        $service = app(GuruService::class);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Kapasitas guru Guru Populer telah penuh (Maksimal 6 siswa aktif).');

        $service->assignSiswa($guru->id, $siswaKe7->id);
    }

    /**
     * Test algoritma cerdas auto-assign (1-klik load balancing):
     * Membagikan siswa belum berguru ke guru yang kompeten dengan beban terendah terlebih dahulu.
     */
    public function test_auto_assign_distributes_unassigned_students_least_loaded_first(): void
    {
        $cabang = Cabang::create([
            'kode' => 'CA_'.mt_rand(1000, 9999),
            'nama' => 'Cabang Auto Assign Test',
            'alamat' => 'Alamat',
            'status' => 'aktif',
        ]);

        // Guru A: SD (beban saat ini 2 siswa)
        $guruA = Guru::create([
            'nama' => 'Guru A (Beban Ringan)',
            'email' => 'guru.a.'.uniqid().'@test.com',
            'no_telp' => '081111111111',
            'jenjang' => 'SD',
            'is_active' => true,
        ]);
        DB::table('guru_cabang')->insert(['guru_id' => $guruA->id, 'cabang_id' => $cabang->id]);
        DB::table('guru_jenjang')->insert(['guru_id' => $guruA->id, 'jenjang' => 'SD']);

        // Guru B: SD (beban saat ini 4 siswa)
        $guruB = Guru::create([
            'nama' => 'Guru B (Beban Sedang)',
            'email' => 'guru.b.'.uniqid().'@test.com',
            'no_telp' => '082222222222',
            'jenjang' => 'SD',
            'is_active' => true,
        ]);
        DB::table('guru_cabang')->insert(['guru_id' => $guruB->id, 'cabang_id' => $cabang->id]);
        DB::table('guru_jenjang')->insert(['guru_id' => $guruB->id, 'jenjang' => 'SD']);

        for ($i = 1; $i <= 2; $i++) {
            Siswa::create([
                'cabang_id' => $cabang->id,
                'nama' => "Siswa Bimbingan Guru A {$i}",
                'tanggal_lahir' => '2015-01-01',
                'alamat' => 'Alamat',
                'nama_ibu' => 'Ibu',
                'no_telp_ortu' => '081234567890',
                'jenjang' => 'SD',
                'kelas' => 4,
                'guru_id' => $guruA->id,
                'status' => 'aktif',
            ]);
        }

        for ($i = 1; $i <= 4; $i++) {
            Siswa::create([
                'cabang_id' => $cabang->id,
                'nama' => "Siswa Bimbingan Guru B {$i}",
                'tanggal_lahir' => '2015-01-01',
                'alamat' => 'Alamat',
                'nama_ibu' => 'Ibu',
                'no_telp_ortu' => '081234567890',
                'jenjang' => 'SD',
                'kelas' => 4,
                'guru_id' => $guruB->id,
                'status' => 'aktif',
            ]);
        }

        // Buat 2 siswa SD tanpa guru
        $unassigned1 = Siswa::create([
            'cabang_id' => $cabang->id,
            'nama' => 'Siswa Unassigned 1',
            'tanggal_lahir' => '2015-01-01',
            'alamat' => 'Alamat',
            'nama_ibu' => 'Ibu',
            'no_telp_ortu' => '081234567890',
            'jenjang' => 'SD',
            'kelas' => 4,
            'guru_id' => null,
            'status' => 'aktif',
        ]);

        $unassigned2 = Siswa::create([
            'cabang_id' => $cabang->id,
            'nama' => 'Siswa Unassigned 2',
            'tanggal_lahir' => '2015-01-01',
            'alamat' => 'Alamat',
            'nama_ibu' => 'Ibu',
            'no_telp_ortu' => '081234567890',
            'jenjang' => 'SD',
            'kelas' => 4,
            'guru_id' => null,
            'status' => 'aktif',
        ]);

        $service = app(GuruService::class);
        $result = $service->autoAssign($cabang->id);

        $this->assertSame(2, $result['assigned_count']);
        $this->assertSame(0, $result['unassigned_count']);

        // Guru A memiliki beban lebih ringan (2 siswa), sehingga siswa pertama harus diarahkan ke Guru A
        $unassigned1->refresh();
        $this->assertSame($guruA->id, $unassigned1->guru_id);
    }
}
