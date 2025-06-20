<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServerTriggerController;

Route::middleware('api')->post('/start', [ServerTriggerController::class, 'startFromExternal']);
