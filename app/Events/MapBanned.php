<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MapBanned implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $lobbyCode;
    public $map;
    public $userId;
    public $finalMap;

     public function __construct($lobbyCode, $map, $userId, $finalMap = null)
    {
        $this->lobbyCode = $lobbyCode;
        $this->map = $map;
        $this->userId = $userId;
        $this->finalMap = $finalMap;
    }

    public function broadcastOn(): Channel
    {
        return new Channel("lobby.{$this->lobbyCode}");
    }

    public function broadcastAs()
    {
        return 'map.banned';
    }
}
