<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_notifications_returns_documented_contract(): void
    {
        $user = User::factory()->create([
            'name' => 'PIC Test',
            'email' => 'pic-notification@example.com',
            'role' => 'user_pic',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/notifications');

        $response->assertOk();
        $response->assertJsonStructure([
            '*' => [
                'id',
                'userId',
                'role',
                'title',
                'message',
                'data',
                'isRead',
                'createdAt',
                'updatedAt',
            ],
        ]);
        $response->assertJsonPath('0.userId', null);
        $response->assertJsonPath('0.role', 'user_pic');
        $response->assertJsonPath('0.title', 'PIC Note');
        $response->assertJsonPath('0.message', 'PIC notification');
        $response->assertJsonPath('0.data', null);
        $response->assertJsonPath('0.isRead', false);
    }

    public function test_mark_notification_read_returns_documented_contract(): void
    {
        $user = User::factory()->create([
            'name' => 'PIC Test',
            'email' => 'pic-notification-read@example.com',
            'role' => 'user_pic',
        ]);

        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/notifications/1/read');

        $response->assertOk();
        $response->assertJsonStructure([
            'id',
            'userId',
            'role',
            'title',
            'message',
            'data',
            'isRead',
            'createdAt',
            'updatedAt',
        ]);
        $response->assertJsonPath('id', 1);
        $response->assertJsonPath('isRead', true);
    }
}
