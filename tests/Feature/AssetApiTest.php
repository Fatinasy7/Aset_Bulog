<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_assets_returns_direct_array_with_camel_case_fields(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin IT',
            'email' => 'admin@example.com',
            'role' => 'admin_it',
        ]);

        Asset::create([
            'kode_aset' => 'AST-100',
            'nama_aset' => 'Laptop Test',
            'merk_type' => 'Dell',
            'serial_number' => 'SN001',
            'lokasi' => 'Gudang A',
            'koordinat_lat' => -6.2,
            'koordinat_lng' => 106.816666,
            'kondisi' => 'baik',
            'tgl_perolehan' => '2024-01-01',
            'harga' => 12000000,
            'keterangan' => 'Test asset',
            'jenis' => 'laptop',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/assets');

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
    }

    public function test_scan_asset_updates_location_and_returns_contract_response(): void
    {
        $user = User::factory()->create([
            'name' => 'PIC Test',
            'email' => 'pic@example.com',
            'role' => 'user_pic',
        ]);

        $asset = Asset::create([
            'kode_aset' => 'AST-200',
            'nama_aset' => 'Printer Test',
            'merk_type' => 'Canon',
            'serial_number' => 'SN002',
            'lokasi' => 'Ruang Tamu',
            'koordinat_lat' => -6.1,
            'koordinat_lng' => 106.7,
            'kondisi' => 'baik',
            'tgl_perolehan' => '2024-02-01',
            'harga' => 5000000,
            'keterangan' => 'Test printer',
            'jenis' => 'printer',
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/assets/{$asset->id}/scan", [
            'latitude' => -6.2,
            'longitude' => 106.816666,
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Scan berhasil, lokasi aset diperbarui.');
        $response->assertJsonPath('asset.id', $asset->id);
        $response->assertJsonPath('asset.koordinat.lat', -6.2);
        $response->assertJsonPath('asset.koordinat.lng', 106.816666);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $response->json('scannedAt'));
        $response->assertJsonStructure([
            'message',
            'asset' => [
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
            'scannedAt',
        ]);

        $this->assertEquals(-6.2, $asset->fresh()->koordinat_lat);
        $this->assertEquals(106.816666, $asset->fresh()->koordinat_lng);
    }

    public function test_get_asset_location_returns_documented_contract(): void
    {
        $user = User::factory()->create([
            'name' => 'PIC Test',
            'email' => 'pic2@example.com',
            'role' => 'user_pic',
        ]);

        $asset = Asset::create([
            'kode_aset' => 'AST-300',
            'nama_aset' => 'Laptop Test 2',
            'merk_type' => 'Lenovo',
            'serial_number' => 'SN003',
            'lokasi' => 'Gudang A',
            'koordinat_lat' => -6.2,
            'koordinat_lng' => 106.816666,
            'kondisi' => 'baik',
            'tgl_perolehan' => '2024-03-01',
            'harga' => 12000000,
            'keterangan' => 'Test asset',
            'jenis' => 'laptop',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/assets/{$asset->id}/location");

        $response->assertOk();
        $response->assertJsonPath('assetId', $asset->id);
        $response->assertJsonPath('lokasi', 'Gudang A');
        $response->assertJsonPath('latitude', -6.2);
        $response->assertJsonPath('longitude', 106.816666);
        $response->assertJsonStructure([
            'assetId',
            'lokasi',
            'latitude',
            'longitude',
            'lastScan' => ['latitude', 'longitude', 'scanned_at'],
        ]);
    }
}
