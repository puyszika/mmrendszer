<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('pickban.{code}', function ($user, $code) {
    return true; // Később ide jöhet, hogy csak a lobby játékosai hallgathatják
});
