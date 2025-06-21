<?php

namespace App\Http\Controllers;

use App\Models\MatchLobby;
use App\Models\MapBan;
use Illuminate\Http\Request;
use App\Events\MapBanned;
use App\Models\GameServer;
use App\Jobs\StartCs2Server;
use Illuminate\Support\Facades\Broadcast;


class LobbyController extends Controller
{
    public function banMap(Request $request, $code)
    {
        $user = auth()->user();
        $map = $request->input('map');

        $lobby = MatchLobby::where('code', $code)->firstOrFail();

        // Ha már bannolva lett
        if (MapBan::where('lobby_code', $code)->where('map', $map)->exists()) {
            return response()->json(['error' => 'Map already banned'], 400);
        }

        // Mentés (még "banned" státusszal)
        MapBan::create([
            'lobby_code' => $code,
            'map' => $map,
            'user_id' => $user->id,
            'action' => 'banned' // vagy később: 'picked'
        ]);

        $bannedMaps = MapBan::where('lobby_code', $code)->pluck('map')->toArray();
        $allMaps = ['mirage', 'inferno', 'nuke', 'overpass', 'vertigo', 'ancient', 'anubis'];
        $remaining = array_values(array_diff($allMaps, $bannedMaps));

        $finalMap = null;
        $nextCaptain = $this->getNextBanTeam($code);

        // Ha már csak 1 map maradt, akkor kiválasztjuk
        if (count($remaining) === 1) {
            $finalMap = $remaining[0];

            // Mentjük is külön bejegyzésként a "picked"-et
            MapBan::create([
                'lobby_code' => $code,
                'map' => $finalMap,
                'user_id' => $user->id,
                'action' => 'picked'
            ]);

            $lobby->update([
                'status' => 'map_chosen',
                'map' => $finalMap
            ]);

            $this->assignServerToLobby($lobby);
        }

        // Mindig fusson le, ne csak az if után!
        event(new MapBanned($code, $map, $user->id, $finalMap, $nextCaptain));

        return response()->json(['success' => true]);

    }

    public function currentTurn($code)
    {
        $lobby = MatchLobby::where('code', $code)->firstOrFail();

        $banCount = MapBan::where('lobby_code', $code)->count();

        $nextUser = ($banCount % 2 === 0)
            ? $lobby->captain_ct_id
            : $lobby->captain_t_id;

        return response()->json(['user_id' => $nextUser]);
    }

     public function show(string $code)
    {
        $lobby = MatchLobby::with(['players.user'])->where('code', $code)->firstOrFail();

        // jogosultság ellenőrzés: csak akkor mutatjuk, ha benne van a lobbyban
        if (!$lobby->players->pluck('user_id')->contains(Auth::id())) {
            abort(403, 'Ehhez a lobbyhoz nincs jogosultságod.');
        }

        return view('lobby.show', compact('lobby'));
    }

    public function assignAvailableServer(MatchLobby $lobby)
    {
        // csak akkor, ha még nincs hozzárendelve szerver
        if ($lobby->server_id) return;

        $server = GameServer::where('is_running', false)->first();

        if (!$server) {
            throw new \Exception("Nincs szabad szerver!");
        }

        $lobby->server_id = $server->id;
        $lobby->save();

        // Indítsuk újra a szervert Laravelből (már csináltuk)
        dispatch(new \App\Jobs\StartCs2Server($server, $lobby)); // ha van ilyen job
    }
    public function assignServerToLobby(MatchLobby $lobby)
    {
        if ($lobby->server_id) return;

        $server = GameServer::where('is_running', false)->first();
        if (!$server) {
            throw new \Exception("Nincs szabad szerver!");
        }

        $lobby->server_id = $server->id;
        $lobby->save();

        dispatch(new StartCs2Server($server, $lobby));
    }
    private function getNextBanTeam($code)
    {
        $lobby = MatchLobby::where('code', $code)->first();
        $ctCaptain = $lobby->players()->where('team', 'ct')->where('is_captain', true)->first();
        $tCaptain = $lobby->players()->where('team', 't')->where('is_captain', true)->first();

        $bans = MapBan::where('lobby_code', $code)->where('action', 'banned')->count();

        // Váltogatás CT ↔ T
        if ($bans % 2 === 0) {
            return $ctCaptain?->user_id;
        } else {
            return $tCaptain?->user_id;
        }
    }

}

