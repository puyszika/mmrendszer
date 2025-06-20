<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameServer extends Model
{
    protected $fillable = ['name', 'port', 'token', 'status'];
}
