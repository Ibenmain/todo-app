<?php

// namespace App\Events;

// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Broadcasting\PrivateChannel;
// use Illuminate\Queue\SerializesModels;
// use App\Models\Notification;

// class NotificationCreated implements ShouldBroadcast
// {
//     use InteractsWithSockets, SerializesModels;

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
