<?php

namespace Tests\Feature;

use App\Models\Asset;
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

        $pic = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'role' => 'user_pic',
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

    public function test_assign_same_pic_returns_asset_unmodified_message(): void
    {
        $admin = User::factory()->create(['role' => 'admin_it']);
        $pic = User::factory()->create(['role' => 'user_pic']);

        $asset = Asset::create([
            'kode_aset' => 'AST-400',
            'nama_aset' => 'Printer Test',
            'merk_type' => 'Canon',
            'serial_number' => 'SN004',
            'lokasi' => 'Gudang B',
            'koordinat_lat' => -6.3,
            'koordinat_lng' => 106.8,
            'kondisi' => 'baik',
            'tgl_perolehan' => '2024-04-01',
            'harga' => 7000000,
            'keterangan' => 'Test assignment',
            'jenis' => 'printer',
            'pic_id' => $pic->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/assets/{$asset->id}/assign-pic", [
                'pic_id' => $pic->id,
                'alasan' => 'Tetap di PIC yang sama',
            ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'PIC sudah ditugaskan pada aset ini.');
        $response->assertJsonPath('asset.id', $asset->id);
        $response->assertJsonPath('asset.picId', $pic->id);
    }

    public function test_asset_qrcode_label_route_returns_svg_label_download(): void
    {
        $admin = User::factory()->create(['role' => 'admin_it']);

        $asset = Asset::create([
            'kode_aset' => 'AST-500',
            'nama_aset' => 'Laptop Label',
            'merk_type' => 'Lenovo',
            'serial_number' => 'SN005',
            'lokasi' => 'Gudang C',
            'koordinat_lat' => -6.4,
            'koordinat_lng' => 106.9,
            'kondisi' => 'baik',
            'tgl_perolehan' => '2024-05-01',
            'harga' => 14000000,
            'keterangan' => 'Test label asset',
            'jenis' => 'laptop',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->get("/api/assets/{$asset->id}/qrcode/label");

        $response->assertOk();
        $response->assertHeader('content-type', 'image/svg+xml');
        $this->assertStringContainsString('attachment; filename=asset-label-', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.svg', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('Label QR Aset', $response->getContent());
        $this->assertStringContainsString($asset->kode_aset, $response->getContent());
        $this->assertStringContainsString($asset->nama_aset, $response->getContent());
        $this->assertStringContainsString($asset->jenis, $response->getContent());
    }

    public function test_asset_qrcode_label_png_returns_png_download(): void
    {
        $admin = User::factory()->create(['role' => 'admin_it']);

        $asset = Asset::create([
            'kode_aset' => 'AST-501',
            'nama_aset' => 'Printer Label PNG',
            'merk_type' => 'Epson',
            'serial_number' => 'SN006',
            'lokasi' => 'Gudang D',
            'koordinat_lat' => -6.5,
            'koordinat_lng' => 107.0,
            'kondisi' => 'baik',
            'tgl_perolehan' => '2024-06-01',
            'harga' => 9000000,
            'keterangan' => 'Test png label asset',
            'jenis' => 'printer',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->get("/api/assets/{$asset->id}/qrcode/label.png");

        $response->assertOk();
        $contentType = $response->headers->get('content-type');
        $this->assertTrue(in_array($contentType, ['image/png', 'image/svg+xml']));
        $this->assertStringContainsString('attachment; filename=asset-label-', $response->headers->get('content-disposition'));

        if ($contentType === 'image/png') {
            $this->assertStringStartsWith("\x89PNG", $response->getContent());
            $this->assertStringContainsString($asset->kode_aset, $response->getContent());
            $this->assertStringContainsString($asset->nama_aset, $response->getContent());
        } else {
            // SVG fallback
            $this->assertStringContainsString('Label QR Aset', $response->getContent());
            $this->assertStringContainsString($asset->kode_aset, $response->getContent());
            $this->assertStringContainsString($asset->nama_aset, $response->getContent());
        }
    }
}
