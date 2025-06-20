<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchLobbyPlayer extends Model
{
    protected $fillable = ['match_lobby_id', 'user_id', 'accepted'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lobby(): BelongsTo
    {
        return $this->belongsTo(MatchLobby::class, 'match_lobby_id');
    }
}
