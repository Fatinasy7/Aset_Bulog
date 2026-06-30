<?php

namespace Tests\Feature;

use App\Mail\InspectionReminderMail;
use App\Models\Asset;
use App\Models\Notification;
use App\Models\Pic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_index_returns_notifications_for_current_user_and_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin_it']);
        $pic = User::factory()->create(['role' => 'user_pic']);

        Notification::create([
            'user_id' => $admin->id,
            'role' => 'admin_it',
            'title' => 'Admin Note',
            'message' => 'Admin-only notification',
        ]);

        Notification::create([
            'user_id' => null,
            'role' => 'user_pic',
            'title' => 'PIC Note',
            'message' => 'PIC notification',
        ]);

        $response = $this->actingAs($pic, 'sanctum')->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'PIC Note']);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'Admin Note']);
    }

    public function test_mark_notification_read_updates_notification(): void
    {
        $admin = User::factory()->create(['role' => 'admin_it']);
        $notification = Notification::create([
            'user_id' => $admin->id,
            'role' => 'admin_it',
            'title' => 'Admin Notification',
            'message' => 'Action required',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(200)
            ->assertJson(['isRead' => true]);

        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_send_inspection_reminders_command_sends_mail_and_creates_notifications(): void
    {
        Mail::fake();

        $pic = Pic::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'telepon' => '08123456789',
        ]);

        Asset::create([
            'kode_aset' => 'LPT-001',
            'nama_aset' => 'MacBook Pro',
            'merk_type' => 'Apple',
            'serial_number' => 'SN12345',
            'lokasi' => 'Gudang',
            'kondisi' => 'baik',
            'tgl_perolehan' => '2024-01-01',
            'harga' => 10000000,
            'keterangan' => 'Test asset',
            'jenis' => 'laptop',
            'pic_id' => $pic->id,
        ]);

        Artisan::call('app:send-inspection-reminders');

        Mail::assertSent(InspectionReminderMail::class, function ($mail) use ($pic) {
            return $mail->hasTo($pic->email);
        });

        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', [
            'role' => 'user_pic',
            'title' => 'Pengingat Pemeriksaan Aset',
        ]);
    }
}
