<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    /**
     * Test admin dashboard renders successfully on home route with concept specs.
     */
    public function test_admin_dashboard_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sang Siroju Lillah');
        $response->assertSee('logo.png');
        $response->assertSee('Dashboard Operasional');
        $response->assertSee('Cabang Yogyakarta Diponegoro');
        $response->assertSee('Auto-Assign');
        $response->assertSee('Kapasitas Slot Guru');
        $response->assertSee('Jadwal Sesi Mengajar Hari Ini');
        $response->assertSee('KW/CAB01/2026/10/0042');
        $response->assertSee('Master Tarif SPP Global');
    }

    /**
     * Test admin dashboard routes aliases work as expected.
     */
    public function test_admin_dashboard_route_aliases(): void
    {
        $responseDashboard = $this->get('/dashboard');
        $responseDashboard->assertStatus(200);

        $responseAdmin = $this->get('/admin');
        $responseAdmin->assertStatus(200);
    }
}
