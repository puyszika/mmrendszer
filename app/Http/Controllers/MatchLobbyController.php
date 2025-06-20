<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchLobby;
use App\Models\MatchLobbyPlayer;
use App\Events\MapBanned;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\GameServerService;



class MatchLobbyController extends Controller
{
    public function show($code)
{
    $lobby = MatchLobby::where('code', $code)->with('players.user')->firstOrFail();
    return view('lobby.match', compact('lobby'));
}

    public function accept($id)
    {
        $userId = auth()->id();

        $player = MatchLobbyPlayer::where('match_lobby_id', $id)->where('user_id', $userId)->firstOrFail();
        $player->accepted = true;
        $player->save();

        $lobby = $player->lobby;

        // Ha MINDENKI accepted -> továbblépés
        if ($lobby->players()->where('accepted', true)->count() === 10) {
            $lobby->status = 'accepted';
            $lobby->started_at = now();
            $lobby->save();

            // 🔥 Itt jöhet a pick&ban trigger vagy szerver indítás

            // Például irányítsuk át egy pick&ban oldalra:
            return redirect()->route('pickban.start', $lobby->code);
        }

        return back()->with('message', 'Elfogadtad a meccset!');
    }
         public function startPickBan($code)
    {
        $lobby = MatchLobby::where('code', $code)->with('players.user')->firstOrFail();

        if (!$lobby->map_pool) {
            $defaultPool = [
                'Mirage', 'Inferno', 'Nuke', 'Anubis', 'Vertigo', 'Ancient', 'Overpass'
            ];

            shuffle($defaultPool); // random sorrend

            $lobby->map_pool = json_encode($defaultPool);
            $lobby->save();
        }

        $mapPool = json_decode($lobby->map_pool, true);

        return view('pickban.index', compact('lobby', 'mapPool'));
    }
    public function banMap(Request $request, $id)
    {
        $lobby = MatchLobby::findOrFail($id);
        $mapToBan = $request->input('map');

        $mapPool = json_decode($lobby->map_pool, true);

        // Eltávolítjuk a bannolt mapet
        $updatedPool = array_values(array_filter($mapPool, fn($map) => $map !== $mapToBan));

        // Ha csak egy map maradt → kiválasztott map + szerver indítás
        if (count($updatedPool) === 1) {
            $lobby->selected_map = $updatedPool[0];
            $lobby->status = 'map_selected';
            $lobby->save();

            // Lekérjük a játékosokat a lobbyban
            $players = $lobby->players()->with('user')->get();

            // Megnézzük, van-e akinek nincs steam_id
            $missingSteamUsers = $players->filter(function ($player) {
                return empty(optional($player->user)->steam_id);
            });

            if ($missingSteamUsers->isNotEmpty()) {
                foreach ($missingSteamUsers as $player) {
                    $username = optional($player->user)->name ?? 'ismeretlen';
                    Log::warning("A szerver nem indult el, mert {$username} játékosnak nincs megadva a Steam ID-ja (player_id={$player->id}).");
                }

                // Állapot frissítése, hogy ne induljon szerver
                $lobby->status = 'missing_steam_ids';
                $lobby->save();

                return redirect()->route('pickban.start', $lobby->code)
                    ->with('error', 'Nem minden játékosnál van beállítva a Steam ID. Kérjük, frissítsd az adatokat.');
            }

            // Ha minden rendben, indítjuk a szervert
            $steamIds = $players->map(fn($p) => $p->user->steam_id)->values();

            // Laravel service hívás
            app(GameServerService::class)->assignFreeServer($steamIds->toArray(), $lobby->selected_map);

            // opcionálisan logolhatod vagy státuszt frissíthetsz
            $lobby->status = 'server_started';
            $lobby->save();

            $service = app(GameServerService::class);
            $server = $service->assignFreeServer($steamIds->toArray(), $lobby->selected_map);

            if (!$server) {
                return redirect()->route('pickban.start', $lobby->code)
                    ->with('error', 'Jelenleg nincs szabad szerver. Kérlek, próbáld újra pár perc múlva.');
            }

            $lobby->status = 'server_started';
            $lobby->save();


        }

        // Menti az új map poolt
        $lobby->map_pool = json_encode($updatedPool);
        $lobby->save();

        // Event kiküldése realtime-hoz
        MapBanned::dispatch($lobby->code, $mapToBan);

        return redirect()->route('pickban.start', $lobby->code);
    }


}
