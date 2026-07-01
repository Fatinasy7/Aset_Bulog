<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetScanTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $regularUser;
    private Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin_it']);
        $this->regularUser = User::factory()->create(['role' => 'user_pic']);
        $this->asset = Asset::factory()->create([
            'lokasi' => 'Gudang',
            'koordinat_lat' => null,
            'koordinat_lng' => null,
        ]);
    }

    /**
     * Test successful QR code scan with geotagging
     */
    public function test_scan_asset_with_coordinates_updates_location(): void
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->postJson("/api/assets/{$this->asset->id}/scan", [
                'latitude' => -6.200000,
                'longitude' => 106.816666,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'asset' => [
                    'id',
                    'kodeAset',
                    'namaAset',
                    'lokasi',
                    'koordinat' => [
                        'lat',
                        'lng',
                    ],
                ],
                'scannedAt',
            ]);

        $this->assertDatabaseHas('assets', [
            'id' => $this->asset->id,
            'koordinat_lat' => -6.200000,
            'koordinat_lng' => 106.816666,
        ]);

        $this->assertDatabaseHas('asset_histories', [
            'asset_id' => $this->asset->id,
            'user_id' => $this->regularUser->id,
            'field_changed' => 'scan',
        ]);
    }

    /**
     * Test scan endpoint requires authentication
     */
    public function test_scan_endpoint_requires_authentication(): void
    {
        $response = $this->postJson("/api/assets/{$this->asset->id}/scan", [
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test scan validation for latitude and longitude
     */
    public function test_scan_validates_coordinates(): void
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->postJson("/api/assets/{$this->asset->id}/scan", [
                'latitude' => 'invalid',
                'longitude' => 'invalid',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['latitude', 'longitude']);
    }

    /**
     * Test scan with custom scanned_by user
     */
    public function test_scan_with_custom_scanned_by_user(): void
    {
        $otherUser = User::factory()->create(['role' => 'user_pic']);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson("/api/assets/{$this->asset->id}/scan", [
                'latitude' => -6.300000,
                'longitude' => 106.900000,
                'scanned_by' => $otherUser->id,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('asset_histories', [
            'asset_id' => $this->asset->id,
            'user_id' => $otherUser->id,
            'field_changed' => 'scan',
        ]);
    }

    /**
     * Test scan with custom scanned_at timestamp
     */
    public function test_scan_with_custom_timestamp(): void
    {
        $customTime = '2026-06-23 10:30:00';

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->postJson("/api/assets/{$this->asset->id}/scan", [
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'scanned_at' => $customTime,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'scannedAt' => $customTime,
            ]);
    }

    /**
     * Test location endpoint returns asset location info
     */
    public function test_get_asset_location(): void
    {
        $this->asset->update([
            'koordinat_lat' => -6.200000,
            'koordinat_lng' => 106.816666,
        ]);

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson("/api/assets/{$this->asset->id}/location");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'assetId',
                'lokasi',
                'latitude',
                'longitude',
                'lastScan',
            ])
            ->assertJson([
                'assetId' => $this->asset->id,
                'lokasi' => 'Gudang',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
            ]);
    }

    /**
     * Test location endpoint requires authentication
     */
    public function test_location_endpoint_requires_authentication(): void
    {
        $response = $this->getJson("/api/assets/{$this->asset->id}/location");

        $response->assertStatus(401);
    }

    /**
     * Test location endpoint returns last scan info
     */
    public function test_location_returns_last_scan_info(): void
    {
        $this->asset->update([
            'koordinat_lat' => -6.200000,
            'koordinat_lng' => 106.816666,
        ]);

        $scanData = [
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'scanned_at' => now()->toDateTimeString(),
        ];

        AssetHistory::create([
            'asset_id' => $this->asset->id,
            'user_id' => $this->adminUser->id,
            'field_changed' => 'scan',
            'new_value' => json_encode($scanData),
        ]);

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson("/api/assets/{$this->asset->id}/location");

        $response->assertStatus(200)
            ->assertJsonPath('lastScan.latitude', -6.200000)
            ->assertJsonPath('lastScan.longitude', 106.816666);
    }

    /**
     * Test multiple scans update asset location history
     */
    public function test_multiple_scans_create_history_records(): void
    {
        $coordinates = [
            [-6.200000, 106.816666],
            [-6.250000, 106.850000],
            [-6.300000, 106.900000],
        ];

        foreach ($coordinates as [$lat, $lng]) {
            $this->actingAs($this->regularUser, 'sanctum')
                ->postJson("/api/assets/{$this->asset->id}/scan", [
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]);
        }

        $histories = AssetHistory::where('asset_id', $this->asset->id)
            ->where('field_changed', 'scan')
            ->get();

        $this->assertEquals(3, $histories->count());
        $this->assertEquals(-6.300000, $this->asset->fresh()->koordinat_lat);
        $this->assertEquals(106.900000, $this->asset->fresh()->koordinat_lng);
    }

    /**
     * Test scan updates asset location from null to value
     */
    public function test_scan_updates_null_coordinates(): void
    {
        $this->asset->update([
            'koordinat_lat' => null,
            'koordinat_lng' => null,
        ]);

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->postJson("/api/assets/{$this->asset->id}/scan", [
                'latitude' => -6.200000,
                'longitude' => 106.816666,
            ]);

        $response->assertStatus(200);

        $updated = $this->asset->fresh();
        $this->assertNotNull($updated->koordinat_lat);
        $this->assertNotNull($updated->koordinat_lng);
    }

    /**
     * Test scan endpoint with non-existent asset returns 404
     */
    public function test_scan_nonexistent_asset_returns_404(): void
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->postJson('/api/assets/99999/scan', [
                'latitude' => -6.200000,
                'longitude' => 106.816666,
            ]);

        $response->assertStatus(404);
    }

    /**
     * Test location endpoint with non-existent asset returns 404
     */
    public function test_location_nonexistent_asset_returns_404(): void
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson('/api/assets/99999/location');

        $response->assertStatus(404);
    }
}
