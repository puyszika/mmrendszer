<?php

namespace App\Services;

use App\Models\GameServer;
use Illuminate\Support\Facades\File;

class GameServerService
{
    public function assignFreeServer(array $steamIds, string $map): ?GameServer
    {
        $server = GameServer::where('status', 'available')->first();

        if (!$server) {
            return null; // nincs szabad szerver
        }

        // 1. útvonalak
        $configPath = "/root/cs2/servers/{$server->name}/cfg";

        // 2. autoexec.cfg generálása
        $autoexec = <<<CFG
map $map
exec whitelist.txt
mp_maxrounds 30
mp_overtime_enable 1
mp_startmoney 800
CFG;

        File::ensureDirectoryExists($configPath);
        File::put("{$configPath}/autoexec.cfg", $autoexec);

        // 3. whitelist.txt létrehozás
        $whitelist = implode("\n", $steamIds);
        File::put("{$configPath}/whitelist.txt", $whitelist);

        // 4. státusz beállítás
        $server->update(['status' => 'busy']);

        // 5. konténer restart
        exec("docker restart {$server->name}");

        return $server;
    }

    public function releaseServer(GameServer $server): void
    {
        $server->update(['status' => 'available']);
    }
}
