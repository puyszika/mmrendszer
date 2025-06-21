<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



//mmhez a controller
use App\Http\Controllers\QueueController;
use App\Http\Controllers\MatchLobbyController;
use App\Http\Controllers\Admin\ServerController;
use App\Http\Controllers\LobbyController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//AZ ÚJ ROUTE mmhez
Route::middleware(['auth'])->group(function () {
    Route::post('/queue/join', [QueueController::class, 'join'])->name('queue.join');
    Route::post('/queue/leave', [QueueController::class, 'leave'])->name('queue.leave');
    Route::get('/match-lobby/{code}', [MatchLobbyController::class, 'show'])->name('lobby.show');
    Route::post('/match-lobby/{id}/accept', [MatchLobbyController::class, 'accept'])->name('lobby.accept');
    Route::get('/pickban/{code}', [MatchLobbyController::class, 'startPickBan'])->name('pickban.start');
    Route::post('/pickban/{id}/ban', [MatchLobbyController::class, 'banMap'])->name('pickban.ban');


});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/servers', [ServerController::class, 'index'])->name('admin.servers.index');

    Route::post('/servers/{instance}/start', [ServerController::class, 'start'])->name('admin.servers.start');
    Route::post('/servers/{instance}/stop', [ServerController::class, 'stop'])->name('admin.servers.stop');
    Route::post('/servers/{instance}/restart', [ServerController::class, 'restart'])->name('admin.servers.restart');
    Route::get('/servers/{instance}/config', [ServerController::class, 'editConfig'])->name('admin.servers.config');
    Route::post('/servers/{instance}/config', [ServerController::class, 'saveConfig'])->name('admin.servers.config.save');
    Route::get('/servers/{instance}/whitelist', [ServerController::class, 'editWhitelist'])->name('admin.servers.whitelist');
    Route::post('/servers/{instance}/whitelist', [ServerController::class, 'saveWhitelist'])->name('admin.servers.whitelist.save');
    Route::get('/servers/{instance}/console/log', [ServerController::class, 'consoleLog'])->name('admin.servers.console.log');
    Route::get('/servers/{instance}/console/exec', [ServerController::class, 'runConsole'])->name('admin.servers.console.exec');
    Route::get('/servers/{instance}/console', [ServerController::class, 'console'])->name('admin.servers.console');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/lobby/{code}/ban-map', [LobbyController::class, 'banMap']);
    Route::get('/lobby/{code}/current-turn', [LobbyController::class, 'currentTurn']);
});

Route::post('/lobby/{code}/start-server', [\App\Http\Controllers\Admin\ServerController::class, 'startServerFromLobby'])
    ->middleware('auth')
    ->name('lobby.startServer');

Route::get('/admin/lobby/{code}', [\App\Http\Controllers\Admin\ServerController::class, 'showLobby'])
    ->middleware('auth')
    ->name('lobby.show');

    
Route::get('/lobby/{code}', [\App\Http\Controllers\LobbyController::class, 'show'])->middleware('auth')->name('lobby.show');



require __DIR__.'/auth.php';
