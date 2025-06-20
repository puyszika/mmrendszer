<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MatchLobby;
use App\Models\MatchLobbyPlayer;
use App\Models\MatchmakingQueue;
use Illuminate\Support\Str;

class MatchmakingSearchCommand extends Command
{
    protected $signature = 'matchmaking:search';
    protected $description = 'Keres 10 játékost és összeállít meccset';

    public function handle()
    {
        $players = MatchmakingQueue::orderBy('mmr')->limit(10)->get();

        if ($players->count() === 10) {
             $lobby = MatchLobby::create([
            'code' => Str::uuid(),
            'status' => 'pending',
        ]);

        foreach ($players as $player) {
            MatchLobbyPlayer::create([
                'match_lobby_id' => $lobby->id,
                'user_id' => $player->user_id,
            ]);

            // Töröljük a queue-ból
            $player->delete();
        }

        $this->info("✅ Lobby létrehozva (ID: {$lobby->id}, code: {$lobby->code})");
        } else {
            $this->info("⏳ Jelenleg {$players->count()} játékos van a queue-ban.");
        }
    }
}
