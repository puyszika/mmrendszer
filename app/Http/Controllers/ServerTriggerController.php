<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchLobby;
use Illuminate\Support\Facades\Http;

class ServerTriggerController extends Controller
{
    public function start(MatchLobby $lobby)
    {
        if (!$lobby->selected_map) {
            return back()->with('error', 'Még nincs kiválasztva pálya!');
        }

        $players = $lobby->players()->with('user')->get();
        $steamIds = $players->pluck('user.steam_id')->filter()->values();

        $response = Http::post('http://your-docker-api-server.local/api/start', [
            'map' => $lobby->selected_map,
            'steam_ids' => $steamIds,
            'lobby_code' => $lobby->code,
        ]);

        if ($response->successful()) {
            return back()->with('message', '✅ Szerver elindítva!');
        } else {
            return back()->with('error', '❌ Szerverindítás sikertelen.');
        }
    }
}
