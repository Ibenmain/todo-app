<?php

// namespace App\Events;

// use App\Models\Notification;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Broadcasting\PrivateChannel;

// class TaskCreated implements ShouldBroadcast
// {
//     use InteractsWithSockets;

//     public $notification;

//     public function __construct(Notification $notification)
//     {
//         $this->notification = $notification;
//     }

//     public function broadcastOn()
//     {
//         return new PrivateChannel('user.' . $this->notification->user_id);
//     }

//     public function broadcastAs()
//     {
//         return 'notification.created';
//     }
// }
