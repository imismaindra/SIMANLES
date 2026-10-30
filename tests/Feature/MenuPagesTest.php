<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\PembayaranSpp;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_dashboard_page_loads_successfully(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Operasional');
        $response->assertSee('Sang Siroju Lillah');
    }

    public function test_siswa_page_loads_successfully(): void
    {
        $response = $this->get('/siswa');

        $response->assertStatus(200);
        $response->assertSee('Data & Pendaftaran Siswa');
        $response->assertSee('Form Pendaftaran Siswa Baru');
    }

    public function test_guru_page_loads_successfully(): void
    {
        $response = $this->get('/guru');

        $response->assertStatus(200);
        $response->assertSee('Distribusi & Kapasitas Guru');
        $response->assertSee('Batas Ketat: Max 6 Siswa / Guru');
    }

    public function test_jadwal_page_loads_successfully(): void
    {
        $response = $this->get('/jadwal');

        $response->assertStatus(200);
        $response->assertSee('Jadwal Mengajar');
        $response->assertSee('Proteksi Anti-Bentrok Lintas Cabang Aktif');
    }

    public function test_presensi_page_loads_successfully(): void
    {
        $response = $this->get('/presensi');

        $response->assertStatus(200);
        $response->assertSee('Presensi Guru & Layar QR Code');
        $response->assertSee('Kode Token Presensi:');
    }

    public function test_spp_page_loads_successfully(): void
    {
        $response = $this->get('/spp');

        $response->assertStatus(200);
        $response->assertSee('Tagihan & SPP Siswa');
        $response->assertSee('Generate Tagihan Massal');
    }

    public function test_kas_page_loads_successfully(): void
    {
        $response = $this->get('/kas');

        $response->assertStatus(200);
        $response->assertSee('Buku Kas & Keuangan');
        $response->assertSee('Daftar Mutasi Buku Kas');
    }

    public function test_pengaturan_page_loads_successfully(): void
    {
        $response = $this->get('/pengaturan');

        $response->assertStatus(200);
        $response->assertSee('Master Data & Pengaturan Cabang');
        $response->assertSee('Master Tarif SPP Global');
    }

    public function test_kwitansi_page_loads_successfully(): void
    {
        $spp = PembayaranSpp::first();
        if (!$spp) {
            $cabang = Cabang::first();
            $siswa = Siswa::first();
            $spp = PembayaranSpp::create([
                'siswa_id' => $siswa->id,
                'cabang_id' => $cabang->id,
                'bulan' => 10,
                'tahun' => 2026,
                'nominal' => 150000,
                'status' => 'lunas',
                'no_kwitansi' => 'KW/CAB-PNK/2026/10/0001',
            ]);
        }

        $response = $this->get("/spp/{$spp->id}/kwitansi");

        $response->assertStatus(200);
        $response->assertSee('KWITANSI RESMI');
        $response->assertSee('LKP Sang Siroju Lillah');
    }
}
