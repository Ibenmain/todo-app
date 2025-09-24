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
        $notifications = Notification::orderBy('created_at', 'desc')->get();

        return response()->json($notifications);
    }
}
