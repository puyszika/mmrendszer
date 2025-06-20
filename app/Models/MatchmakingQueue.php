<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchmakingQueue extends Model
{
    protected $fillable = ['user_id', 'mmr', 'joined_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
