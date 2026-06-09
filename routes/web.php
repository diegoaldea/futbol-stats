<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [GameController::class, 'index'])->name('home');
Route::get('/games/create', [GameController::class, 'create'])->name('games.create');
Route::post('/games', [GameController::class, 'store'])->name('games.store');
Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');

Route::post('/players', [PlayerController::class, 'store'])->name('players.store');

Route::post('/games/{game}/stats', [GameController::class, 'addStat'])->name('games.stats.add');


require __DIR__.'/auth.php';
