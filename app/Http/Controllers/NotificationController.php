<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ApiResponseFormatter;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponseFormatter;

    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = Notification::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('role', $user->role);
        })->orderBy('created_at', 'desc')->get();

        return response()->json($notifications->map(fn ($notification) => $this->formatNotificationPayload($notification)));
    }

    protected function formatNotificationPayload(Notification $notification): array
    {
        return [
            'id' => $notification->id,
            'userId' => $notification->user_id,
            'role' => $notification->role,
            'title' => $notification->title,
            'message' => $notification->message,
            'data' => $notification->data,
            'isRead' => $notification->is_read,
            'createdAt' => $notification->created_at instanceof \DateTimeInterface ? $notification->created_at->format(\DateTimeInterface::ATOM) : null,
            'updatedAt' => $notification->updated_at instanceof \DateTimeInterface ? $notification->updated_at->format(\DateTimeInterface::ATOM) : null,
        ];
    }

    public function markRead(Request $request, Notification $notification)
    {
        $user = $request->user();

        if ($notification->user_id && $notification->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($notification->role && $notification->role !== $user->role) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json($this->formatNotificationPayload($notification));
    }
}
