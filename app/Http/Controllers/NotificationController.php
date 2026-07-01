<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected function buildNotification($notificationId, bool $isRead = false): array
    {
        return [
            'id' => (int) $notificationId,
            'userId' => null,
            'role' => 'user_pic',
            'title' => 'PIC Note',
            'message' => 'PIC notification',
            'data' => null,
            'isRead' => $isRead,
            'createdAt' => now()->toISOString(),
            'updatedAt' => now()->toISOString(),
        ];
    }

    public function index(Request $request)
    {
        $notifications = [
            $this->buildNotification(1),
        ];

        return response()->json($notifications);
    }

    public function markRead(Request $request, $notification)
    {
        return response()->json($this->buildNotification($notification, true));
    }
}
