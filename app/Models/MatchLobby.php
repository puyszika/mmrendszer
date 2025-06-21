<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\MatchLobby;

class MatchLobby extends Model
{
    protected $fillable = ['code', 'status', 'started_at', 'match_id', 'captain_t_id', 'captain_ct_id', 'final_map'];

    public function players()
    {
        return $this->hasMany(\App\Models\MatchLobbyPlayer::class, 'match_lobby_id');
    }

    public function captainCT()
    {
        return $this->belongsTo(User::class, 'captain_ct_id');
    }

    public function captainT()
    {
        return $this->belongsTo(User::class, 'captain_t_id');
    }

    public function gameServer()
    {
        return $this->belongsTo(\App\Models\GameServer::class, 'game_server_id');
    }

    public function server()
    {
        return $this->belongsTo(GameServer::class);
    }

    public function mapBans()
    {
        return $this->hasMany(MapBan::class, 'lobby_code', 'code');
    }

}
