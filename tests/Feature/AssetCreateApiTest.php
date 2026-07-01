<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetCreateApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_asset_returns_camel_case_payload_with_qr_code_path(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin IT',
            'email' => 'admin@example.com',
            'role' => 'admin_it',
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/assets', [
            'kode_aset' => 'AST-101',
            'nama_aset' => 'Printer Test',
            'merk_type' => 'HP',
            'serial_number' => 'SN002',
            'lokasi' => 'Gudang B',
            'koordinat_lat' => -6.3,
            'koordinat_lng' => 106.9,
            'kondisi' => 'baik',
            'tgl_perolehan' => '2024-02-01',
            'harga' => 4500000,
            'keterangan' => 'Printer test',
            'jenis' => 'printer',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
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
        ]);
    }
}
