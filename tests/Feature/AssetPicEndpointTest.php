<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Pic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetPicEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/assets');

        $response->assertStatus(401);
    }

    public function test_asset_index_returns_assets_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'admin_it']);

        $asset = Asset::factory()->create([
            'kode_aset' => 'AST-100',
            'nama_aset' => 'Laptop Test',
            'merk_type' => 'Dell',
            'serial_number' => 'SN001',
            'lokasi' => 'Gudang A',
            'koordinat_lat' => -6.200000,
            'koordinat_lng' => 106.816666,
            'kondisi' => 'baik',
            'tgl_perolehan' => '2024-01-01',
            'harga' => 12000000,
            'keterangan' => 'Test asset',
            'jenis' => 'laptop',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/assets');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.kodeAset', 'AST-100')
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'kodeAset',
                    'namaAset',
                    'merkType',
                    'serialNumber',
                    'lokasi',
                    'koordinat' => [
                        'lat',
                        'lng',
                    ],
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

    public function test_pic_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/pics');

        $response->assertStatus(401);
    }

    public function test_pic_index_returns_pics_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'admin_it']);

        $pic = Pic::create([
            'nama' => 'Budi Santoso',
            'jabatan' => 'PIC',
            'email' => 'budi.santoso@example.com',
            'telepon' => '081234567890',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/pics');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.nama', 'Budi Santoso')
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'nama',
                    'jabatan',
                    'email',
                    'telepon',
                    'createdAt',
                    'updatedAt',
                ],
            ]);
    }
}
