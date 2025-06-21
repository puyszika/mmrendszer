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

    public string $lobbyCode;
    public string $map;
    public int $userId;
    public ?string $finalMap;
    public ?int $nextCaptain;

    public function __construct(string $code, string $map, int $userId, ?string $finalMap = null, ?int $nextCaptain = null)
    {
        $this->lobbycode = $code;
        $this->map = $map;
        $this->userId = $userId;
        $this->finalMap = $finalMap;
        $this->nextCaptain = $nextCaptain;
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
