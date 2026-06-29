<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Pic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_index_eager_loads_related_pic_without_n_plus_one_queries(): void
    {
        $user = User::factory()->create(['role' => 'admin_it']);
        $pic = Pic::create([
            'nama' => 'Budi Santoso',
            'jabatan' => 'PIC IT',
            'email' => 'budi@example.com',
            'telepon' => '081234567890',
        ]);

        Asset::create([
            'kode_aset' => 'AST-001',
            'nama_aset' => 'Laptop Test',
            'merk_type' => 'Dell',
            'serial_number' => 'SN-001',
            'lokasi' => 'Gudang',
            'kondisi' => 'baik',
            'jenis' => 'laptop',
            'pic_id' => $pic->id,
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/assets');

        $response->assertOk();
        $response->assertJsonStructure([
            '*' => [
                'id',
                'kode_aset',
                'pic' => ['id', 'nama'],
            ],
        ]);

        $this->assertLessThanOrEqual(2, count(DB::getQueryLog()));
    }
}
