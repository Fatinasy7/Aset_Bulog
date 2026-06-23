<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = Notification::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('role', $user->role);
        })->orderBy('created_at', 'desc')->get();

        return response()->json($notifications);
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

        return response()->json($notification);
    }
}
