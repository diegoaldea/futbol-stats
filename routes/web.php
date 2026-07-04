<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');

    Route::get('/rendimiento', [GameController::class, 'rendimiento'])->name('rendimiento');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/stats', [\App\Http\Controllers\Admin\StatController::class, 'index'])->name('stats.index');
    Route::post('/stats', [\App\Http\Controllers\Admin\StatController::class, 'store'])->name('stats.store');
    Route::put('/stats/{stat}', [\App\Http\Controllers\Admin\StatController::class, 'update'])->name('stats.update');
    Route::delete('/stats/{stat}', [\App\Http\Controllers\Admin\StatController::class, 'destroy'])->name('stats.destroy');

    Route::post('/categories', [\App\Http\Controllers\Admin\StatCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{statCategory}', [\App\Http\Controllers\Admin\StatCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{statCategory}', [\App\Http\Controllers\Admin\StatCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/games', [\App\Http\Controllers\Admin\GameController::class, 'index'])->name('games.index');
    Route::delete('/games/{game}', [\App\Http\Controllers\Admin\GameController::class, 'destroy'])->name('games.destroy');
});

Route::get('/', [GameController::class, 'index'])->name('home');
Route::get('/games/create', [GameController::class, 'create'])->name('games.create');
Route::post('/games', [GameController::class, 'store'])->name('games.store');
Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');

Route::post('/players', [PlayerController::class, 'store'])->name('players.store');

Route::post('/games/{game}/stats', [GameController::class, 'addStat'])->name('games.stats.add');
Route::post('/games/{game}/finish', [GameController::class, 'finish'])->name('games.finish');
Route::get('/games/{game}/summary', [GameController::class, 'summary'])->name('games.summary');


require __DIR__.'/auth.php';
