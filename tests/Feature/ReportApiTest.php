<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_report_preview_returns_camel_case_assets(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin IT',
            'email' => 'admin-report@example.com',
            'role' => 'admin_it',
        ]);

        Asset::create([
            'kode_aset' => 'AST-400',
            'nama_aset' => 'Laptop Report',
            'merk_type' => 'Lenovo',
            'serial_number' => 'SN400',
            'lokasi' => 'Gudang A',
            'koordinat_lat' => -6.2,
            'koordinat_lng' => 106.8,
            'kondisi' => 'baik',
            'tgl_perolehan' => '2024-01-01',
            'harga' => 10000000,
            'keterangan' => 'Test report',
            'jenis' => 'laptop',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/reports/assets?kondisi=baik&jenis=laptop&lokasi=Gudang');

        $response->assertOk();
        $response->assertJsonStructure([
            '*' => [
                'id',
                'kodeAset',
                'namaAset',
                'merkType',
                'serialNumber',
                'lokasi',
                'koordinat' => ['lat', 'lng'],
                'kondisi',
                'tglPerolehan',
                'harga',
                'keterangan',
                'jenis',
                'qrCodePath',
                'picId',
                'pic',
                'createdAt',
                'updatedAt',
            ],
        ]);
        $response->assertJsonPath('0.kodeAset', 'AST-400');
        $response->assertJsonPath('0.kondisi', 'baik');
    }

    public function test_asset_report_excel_export_returns_downloadable_file(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin IT',
            'email' => 'admin-report-export@example.com',
            'role' => 'admin_it',
        ]);

        Asset::create([
            'kode_aset' => 'AST-500',
            'nama_aset' => 'Printer Report',
            'merk_type' => 'Canon',
            'serial_number' => 'SN500',
            'lokasi' => 'Gudang B',
            'koordinat_lat' => -6.3,
            'koordinat_lng' => 106.9,
            'kondisi' => 'baik',
            'tgl_perolehan' => '2024-02-01',
            'harga' => 5000000,
            'keterangan' => 'Test report export',
            'jenis' => 'printer',
        ]);

        $response = $this->actingAs($user, 'sanctum')->get('/api/reports/assets?format=excel');

        $response->assertOk();
        $response->assertHeader('content-disposition', 'attachment; filename=aset-report.xlsx');
    }
}
