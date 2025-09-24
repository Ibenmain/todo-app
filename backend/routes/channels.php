<?php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});



Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    // Only allow access if the authenticated user ID matches the channel userId
    return (int) $user->id === (int) $userId;
});
