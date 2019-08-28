<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PMSHasChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $user;
    protected $for_employee;

    public function __construct($user,$for_employee)
    {
        $this->user=$user;
        $this->for_employee=$for_employee;
    }

    public function broadcastAs()
    {
        return 'pms-has-changed-'.$this->for_employee->id;
    }

    public function broadcastOn()
    {
        return new Channel($this->user->getChannel());
    }
}
