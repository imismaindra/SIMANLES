<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure guest state for login tests
        Auth::logout();
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SIMANLES');
        $response->assertSee('LKP Sang Siroju Lillah');
        $response->assertSee('Admin Pusat');
        $response->assertSee('Pimp. Cabang');
        $response->assertSee('Guru Pengajar');
        $response->assertSee('Masuk ke Portal');
    }

    public function test_user_can_login_with_admin_credentials(): void
    {
        $response = $this->post('/login', [
            'identifier' => 'admin@lespintar.id',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_guru_can_login_and_redirects_to_jadwal(): void
    {
        $response = $this->post('/login', [
            'identifier' => 'nip3101@lespintar.id',
            'password' => '1995-05-14',
        ]);

        $response->assertRedirect(route('jadwal.index'));
        $this->assertAuthenticated();
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->from('/login')->post('/login', [
            'identifier' => 'admin@lespintar.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('identifier');
        $this->assertGuest();
    }

    public function test_guru_login_ignores_admin_intended_url_and_avoids_403(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');

        $response = $this->followingRedirects()->post('/login', [
            'identifier' => 'nip3101@lespintar.id',
            'password' => '1995-05-14',
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticated();
        $this->assertSame(route('jadwal.index'), url()->current());
    }

    public function test_cabang_login_ignores_pusat_only_intended_url_and_avoids_403(): void
    {
        $this->get('/pengaturan')->assertRedirect('/login');

        $response = $this->followingRedirects()->post('/login', [
            'identifier' => 'cabang.ponokawan@lespintar.id',
            'password' => 'cabang123',
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticated();
        $this->assertSame(route('admin.dashboard'), url()->current());
    }

    public function test_same_role_intended_url_is_preserved_after_login(): void
    {
        $this->get('/presensi/scan')->assertRedirect('/login');

        $response = $this->post('/login', [
            'identifier' => 'nip3101@lespintar.id',
            'password' => '1995-05-14',
        ]);

        $response->assertRedirect(url('/presensi/scan'));
        $this->assertAuthenticated();
    }

    public function test_authenticated_guru_visiting_login_redirects_to_jadwal_without_403(): void
    {
        $guru = User::where('role', 'guru')->firstOrFail();

        $response = $this->actingAs($guru)->followingRedirects()->get('/login');

        $response->assertStatus(200);
        $this->assertSame(route('jadwal.index'), url()->current());
    }
}
