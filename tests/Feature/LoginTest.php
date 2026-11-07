<?php

namespace Tests\Feature;

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
}
