<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Inertia\Inertia;

class GameController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Games', [
            'games' => Game::with('user')->latest('date')->get(),
        ]);
    }

    public function destroy(Game $game)
    {
        $game->delete();

        return back();
    }
}
