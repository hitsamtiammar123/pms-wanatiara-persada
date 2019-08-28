<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    protected $user;

    public function __construct($user,$data)
    {
        $this->user=$user;
        $this->data=$data;
    }

    public function broadcastAs()
    {
        return 'new-notification';
    }

    public function broadcastOn()
    {
        return new Channel($this->user->getChannel());
    }
}
