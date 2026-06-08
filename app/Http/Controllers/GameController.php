<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Game;

class GameController extends Controller
{
    public function index()
    {
        return Inertia::render('Home');
    }

    public function create()
    {
        $players = auth()->check()
            ? auth()->user()->players
            : collect([]);
        
        return Inertia::render('Games/Create', [
            'players' => $players,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'team_a' => 'required|array|min:1',
            'team_b' => 'required|array|min:1',
        ]);

        $game = Game::create([
            'user_id' => auth()->id(),
            'token' => auth()->check() ? null : \Illuminate\Support\Str::random(32),
            'date' => $request->date,
        ]);

        return redirect()->route('games.show', $game->token ?? $game->id);
    }

    public function show(string $id)
    {
        $game = Game::where('id', $id)->orWhere('token', $id)->firstOrFail();
    
        return Inertia::render('Games/Show', [
            'game' => $game,
        ]);
    }

    public function edit(string $id)
    {
        
    }

    public function update(Request $request, string $id)
    {
        
    }

    public function destroy(string $id)
    {
        
    }
}
