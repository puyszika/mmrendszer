<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchLobby extends Model
{
    protected $fillable = ['code', 'status', 'started_at', 'match_id', 'captain_t_id', 'captain_ct_id', 'final_map'];

    public function players(): HasMany
    {
        return $this->hasMany(MatchLobbyPlayer::class);
    }

     public function mapBans()
    {
        return $this->hasMany(MapBan::class);
    }
    public function captainCT()
    {
        return $this->belongsTo(User::class, 'captain_ct_id');
    }

    public function captainT()
    {
        return $this->belongsTo(User::class, 'captain_t_id');
    }
}
