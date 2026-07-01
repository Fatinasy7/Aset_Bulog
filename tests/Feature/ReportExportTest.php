<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin_it']);
    }

    public function test_can_preview_asset_report(): void
    {
        Asset::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/reports/assets');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_download_excel_report(): void
    {
        Asset::factory()->count(2)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->get('/api/reports/assets?format=excel');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('aset-report.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_can_download_pdf_report(): void
    {
        Asset::factory()->count(2)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->get('/api/reports/assets?format=pdf');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('aset-report-', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.pdf', $response->headers->get('content-disposition'));
    }

    public function test_can_download_pdf_report_via_direct_download_route(): void
    {
        Asset::factory()->count(2)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->get('/api/reports/assets/download');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('.pdf', $response->headers->get('content-disposition'));
    }
}
