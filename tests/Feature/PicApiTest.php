<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_pics_returns_direct_array_with_documented_fields(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin IT',
            'email' => 'admin@example.com',
            'role' => 'admin_it',
        ]);

        User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'role' => 'user_pic',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/pics');

        $response->assertOk();
        $response->assertJsonStructure([
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
        $response->assertJsonPath('0.nama', 'Budi Santoso');
        $response->assertJsonPath('0.jabatan', 'PIC');
    }
}
