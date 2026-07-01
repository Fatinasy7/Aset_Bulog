<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Pic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_summary_requires_authentication(): void
    {
        $response = $this->getJson('/api/dashboard/summary');

        $response->assertStatus(401);
    }

    public function test_dashboard_summary_returns_summary_data_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'admin_it']);

        Pic::create([ 'nama' => 'Budi Santoso', 'jabatan' => 'PIC', 'email' => 'budi.santoso@example.com', 'telepon' => '081234567890' ]);
        Pic::create([ 'nama' => 'Siti Aminah', 'jabatan' => 'PIC', 'email' => 'siti.aminah@example.com', 'telepon' => '081234567891' ]);

        Asset::factory()->createMany([
            [
                'kode_aset' => 'AST-101',
                'nama_aset' => 'Laptop A',
                'merk_type' => 'Dell',
                'serial_number' => 'SN101',
                'lokasi' => 'Gudang A',
                'koordinat_lat' => -6.2,
                'koordinat_lng' => 106.8,
                'kondisi' => 'baik',
                'tgl_perolehan' => '2024-01-01',
                'harga' => 12000000,
                'keterangan' => 'Test asset A',
                'jenis' => 'laptop',
            ],
            [
                'kode_aset' => 'AST-102',
                'nama_aset' => 'Printer B',
                'merk_type' => 'HP',
                'serial_number' => 'SN102',
                'lokasi' => 'Gudang B',
                'koordinat_lat' => -6.3,
                'koordinat_lng' => 106.9,
                'kondisi' => 'rusak_berat',
                'tgl_perolehan' => '2024-02-01',
                'harga' => 4500000,
                'keterangan' => 'Test asset B',
                'jenis' => 'printer',
            ],
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/dashboard/summary');

        $response->assertStatus(200)
            ->assertJson([ 
                'totalAssets' => 2,
                'totalLaptops' => 1,
                'totalPrinters' => 1,
                'totalPics' => 2,
            ])
            ->assertJsonStructure([
                'totalAssets',
                'totalLaptops',
                'totalPrinters',
                'totalPics',
                'conditionCounts' => [
                    'BAIK',
                    'RUSAK_RINGAN',
                    'RUSAK_BERAT',
                    'DALAM_PERBAIKAN',
                    'TIDAK_AKTIF',
                ],
                'latestAssets',
            ]);
    }
}
