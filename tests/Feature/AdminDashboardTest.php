<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    /**
     * Test admin dashboard renders successfully on home route.
     */
    public function test_admin_dashboard_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SIMANLES');
        $response->assertSee('Dashboard Operasional');
        $response->assertSee('Sesi Pembelajaran Hari Ini');
        $response->assertSee('Cabang Utama Diponegoro');
        $response->assertSee('Realisasi SPP', false);
        $response->assertSee('Tentor On-Duty Hari Ini');
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
