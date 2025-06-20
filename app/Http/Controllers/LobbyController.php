<?php

namespace App\Http\Controllers;

use App\Models\MatchLobby;
use App\Models\MapBan;
use Illuminate\Http\Request;
use App\Events\MapBanned;


class LobbyController extends Controller
{
    public function banMap(Request $request, $code)
    {
        $user = auth()->user();
        $map = $request->input('map');

        $lobby = MatchLobby::where('code', $code)->firstOrFail();

        if (MapBan::where('lobby_code', $code)->where('map', $map)->exists()) {
            return response()->json(['error' => 'Map already banned'], 400);
        }

        MapBan::create([
            'lobby_code' => $code,
            'map' => $map,
            'user_id' => $user->id,
        ]);

        $bannedMaps = MapBan::where('lobby_code', $code)->pluck('map')->toArray();
        $allMaps = ['mirage', 'inferno', 'nuke', 'overpass', 'vertigo', 'ancient', 'anubis'];
        $remaining = array_values(array_diff($allMaps, $bannedMaps));

        $finalMap = null;
        if (count($remaining) === 1) {
            $finalMap = $remaining[0];
            $lobby->update(['status' => 'map_chosen']); // vagy akár: $lobby->map = $finalMap
        }

        broadcast(new MapBanned($code, $map, $user->id, $finalMap))->toOthers();

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
}

