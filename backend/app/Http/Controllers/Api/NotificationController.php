<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\NotificationEvent;


class NotificationController extends Controller
{
    public function getAllNotifications()
    {
        error_log("Fetching all notifications");
        // get not read notifications
        $notifications = Notification::where('read', false)->orderBy('created_at', 'desc')->get();

        return response()->json($notifications);
    }

    public function markAsRead(Request $request )
    {
        $id = $request->id;
        error_log("Marking notification $id as read");

        $notification = Notification::find($id);
        if ($notification) {
            $notification->read = true;
            $notification->save();
            return response()->json(['message' => 'Notification marked as read']);
        }
        return response()->json(['message' => 'Notification not found'], 404);
    }

    public function markAllAsRead()
    {
        Notification::where('read', false)->update(['read' => true]);
        return response()->json(['message' => 'All notifications marked as read']);
    }
}
