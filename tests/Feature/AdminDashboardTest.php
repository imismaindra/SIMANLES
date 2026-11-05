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
        $response->assertSee('Cabang Ponokawan');
        $response->assertSee('Auto-Assign');
        $response->assertSee('Kapasitas Slot Guru');
        $response->assertSee('Jadwal Sesi Mengajar Hari Ini');
        $response->assertSee('KW/CAB-PNK/2026/10/');
        $response->assertSee('Master Tarif SPP Global');

        // Verify teachers from SEEDER_DATA.md
        $response->assertSee('Bu Divani');
        $response->assertSee('Bu Nida');
        $response->assertSee('Bu Annisa');

        // Verify unassigned students from SEEDER_DATA.md
        $response->assertSee('Azkiya');
        $response->assertSee('Dinda');
        $response->assertSee('Gibran');
    }

    /**
     * Test admin dashboard can filter by branch.
     */
    public function test_admin_dashboard_can_switch_branch(): void
    {
        $response = $this->get('/?cabang_id=cab-1');
        $response->assertStatus(200);
        $response->assertSee('Cabang Jakarta Selatan');
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
