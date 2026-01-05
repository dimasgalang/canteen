<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScanProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $canteenNo;

    public function __construct($canteenNo)
    {
        $this->canteenNo = $canteenNo;
    }

    public function broadcastOn()
    {
        return new Channel('canteen');
    }

    public function broadcastAs()
    {
        return 'ScanProcessed';
    }
}
