<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\GamePlayerStat;

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

        foreach ($request->team_a as $player) {
            GamePlayer::create([
                'game_id' => $game->id,
                'player_id' => $player['id'],
                'team' => 'a',
            ]);
        }

        foreach ($request->team_b as $player) {
            GamePlayer::create([
                'game_id' => $game->id,
                'player_id' => $player['id'],
                'team' => 'b',
            ]);
        }

        return redirect()->route('games.show', $game->token ?? $game->id);
    }

    public function show(string $id)
    {
        $game = Game::where('id', $id)->orWhere('token', $id)->firstOrFail();
        
        $teamA = $game->gamePlayers()->where('team', 'a')->with('player')->get();
        $teamB = $game->gamePlayers()->where('team', 'b')->with('player')->get();
        $stats = \App\Models\Stat::where('is_global', true)->with('category')->get();

        return Inertia::render('Games/Show', [
            'game' => $game,
            'teamA' => $teamA,
            'teamB' => $teamB,
            'stats' => $stats,
        ]);
    }

    public function addStat(Request $request, Game $game)
    {
        $request->validate([
            'game_player_id' => 'required|exists:game_players,id',
            'stat_id' => 'required|exists:stats,id',
            'action' => 'required|in:add,subtract',
        ]);

        $gameStat = GamePlayerStat::firstOrCreate([
            'game_player_id' => $request->game_player_id,
            'stat_id' => $request->stat_id,
        ], ['value' => 0]);

        if ($request->action === 'add') {
            $gameStat->increment('value');
        } else {
            if ($gameStat->value > 0) {
                $gameStat->decrement('value');
            }
        }

        return response()->json($gameStat);
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
