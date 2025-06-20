<?php

namespace App\Http\Controllers;

use App\Models\MatchmakingQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MatchLobby;
use Illuminate\Support\Str;

class QueueController extends Controller
{
    public function join()
    {
        $user = Auth::user();

        if (MatchmakingQueue::where('user_id', $user->id)->exists()) {
            return back()->with('message', 'Már bent vagy a matchmakingben!');
        }

        MatchmakingQueue::create([
            'user_id' => $user->id,
            'mmr' => 1000, // később felülírható
        ]);

                // Tegyük fel, hogy itt vannak a 10 játékos user_id-i:
        $playerIds = [14, 22, 37, 41, 5, 19, 11, 3, 28, 9]; // Ezt nyilván dinamikusan töltöd ki máshonnan

        // 1. Megkeverjük a játékosokat
        $shuffled = collect($playerIds)->shuffle();

        // 2. Létrehozzuk a lobbypéldányt, beleírjuk a két kapitányt
        $lobby = MatchLobby::create([
            'code' => Str::uuid(),
            'status' => 'accepted',
            'started_at' => now(),
            'captain_ct_id' => $shuffled[0],
            'captain_t_id' => $shuffled[1],
        ]);

        // 3. (opcionális) Ha van lobby-játékos pivotod, akkor mentsd el a többieket is:
        foreach ($playerIds as $userId) {
            DB::table('lobby_user')->insert([
                'match_lobby_id' => $lobby->id,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

          MatchmakingQueue::whereIn('user_id', $players)->delete();

        return back()->with('message', 'Beléptél a matchmaking queue-ba.');
    }

    public function leave()
    {
        MatchmakingQueue::where('user_id', Auth::id())->delete();

        return back()->with('message', 'Kiléptél a matchmakingből.');
    }
    public function checkAndCreateLobby()
    {
        $players = MatchmakingQueue::limit(10)->pluck('user_id');

        if ($players->count() < 10) {
            return; // Még nem elég játékos
        }

        // 1. Keverjük meg őket
        $shuffled = $players->shuffle();

        // 2. Létrehozzuk a lobbyt
        $lobby = MatchLobby::create([
            'code' => Str::uuid(),
            'status' => 'accepted',
            'started_at' => now(),
            'captain_ct_id' => $shuffled[0],
            'captain_t_id' => $shuffled[1],
        ]);

        // 3. Opcionális: tárold le kik vannak a lobbyban (pl. ha van lobby_user pivotod)

        // 4. Töröljük őket a queue-ból
        MatchmakingQueue::whereIn('user_id', $players)->delete();

        // 5. Átirányítás vagy event/redirect/emit ha kell
    }
}
