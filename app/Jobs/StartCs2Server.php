<?php

namespace App\Jobs;

use App\Models\GameServer;
use App\Models\MatchLobby;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StartCs2Server implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $server;
    protected $lobby;

    public function __construct(GameServer $server, MatchLobby $lobby)
    {
        $this->server = $server;
        $this->lobby = $lobby;
    }

    public function handle(): void
    {
        $cmd = "ssh root@server.versuscs.hu '/mnt/cs2ssd/cs2-multiserver/cs2-server restart {$this->server->name}'";
        $output = shell_exec($cmd);

        Log::info("Szerver újraindítva: {$this->server->name}, kimenet: " . $output);
    }
}
