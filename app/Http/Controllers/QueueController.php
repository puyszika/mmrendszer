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

        // Ellenőrizzük, van-e legalább 10 játékos
        $players = MatchmakingQueue::limit(10)->pluck('user_id');

        if ($players->count() === 10) {
            $shuffled = $players->shuffle();

            $lobby = MatchLobby::create([
                'code' => Str::uuid(),
                'status' => 'accepted',
                'started_at' => now(),
                'captain_ct_id' => $shuffled[0],
                'captain_t_id' => $shuffled[1],
            ]);

            // Töröljük őket a queue-ból
            MatchmakingQueue::whereIn('user_id', $players)->delete();

            // Itt lehet redirect a lobby oldalra, ha szeretnéd:
            // return redirect()->route('lobby.show', ['code' => $lobby->code]);
        }


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
