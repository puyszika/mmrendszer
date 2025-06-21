<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MapBan extends Model
{
    protected $fillable = ['lobby_code', 'map', 'user_id', 'action'];

    public function lobby()
    {
        return $this->belongsTo(MatchLobby::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}