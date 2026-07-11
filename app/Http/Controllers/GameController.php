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
        $games = collect([]);

        if (auth()->check()) {
            $games = auth()->user()->games()
                ->with(['gamePlayers.player', 'gamePlayers.stats.stat'])
                ->latest('date')
                ->get()
                ->map(function (Game $game) {
                    $game->scorers = [
                        'a' => $this->goalScorers($game, 'a'),
                        'b' => $this->goalScorers($game, 'b'),
                    ];

                    // No mandamos todos los jugadores/stats al front, solo los goleadores ya calculados
                    $game->unsetRelation('gamePlayers');

                    return $game;
                });
        }

        return Inertia::render('Home', [
            'games' => $games,
        ]);
    }

    /**
     * Devuelve los goleadores de un equipo: goles propios + goles en contra del rival.
     * [['name' => ..., 'goals' => ..., 'ownGoal' => bool], ...]
     */
    private function goalScorers(Game $game, string $team): array
    {
        $opponent = $team === 'a' ? 'b' : 'a';
        $scorers = [];

        // Goles normales de los jugadores de este equipo
        foreach ($game->gamePlayers->where('team', $team) as $gamePlayer) {
            $gol = $gamePlayer->stats->first(
                fn ($stat) => strtolower($stat->stat->name) === 'gol'
            );

            if ($gol && $gol->value > 0) {
                $scorers[] = [
                    'name' => $gamePlayer->player->name,
                    'goals' => $gol->value,
                    'ownGoal' => false,
                ];
            }
        }

        // Goles en contra de los jugadores del rival (suman para este equipo)
        foreach ($game->gamePlayers->where('team', $opponent) as $gamePlayer) {
            $ownGoal = $gamePlayer->stats->first(
                fn ($stat) => strtolower($stat->stat->name) === 'gol en contra'
            );

            if ($ownGoal && $ownGoal->value > 0) {
                $scorers[] = [
                    'name' => $gamePlayer->player->name,
                    'goals' => $ownGoal->value,
                    'ownGoal' => true,
                ];
            }
        }

        return $scorers;
    }

    public function rendimiento()
    {
        $games = auth()->user()->games()
            ->with(['gamePlayers.player', 'gamePlayers.stats.stat'])
            ->get();

        $players = [];
        $statMeta = [];

        foreach ($games as $game) {
            foreach ($game->gamePlayers as $gamePlayer) {
                $key = $gamePlayer->player_id;
                $stats = [];
                $actions = 0;
                $rating = 6.0; // base por partido

                foreach ($gamePlayer->stats as $s) {
                    $name = $s->stat->name;
                    $points = (float) $s->stat->points;
                    $stats[$name] = ($stats[$name] ?? 0) + $s->value;
                    $actions += $s->value;
                    $rating += $s->value * $points;
                    $statMeta[$name] = $points;
                }

                if (! isset($players[$key])) {
                    $players[$key] = [
                        'id' => $gamePlayer->player_id,
                        'name' => $gamePlayer->player->name,
                        'matches' => [],
                    ];
                }

                $players[$key]['matches'][] = [
                    'stats' => (object) $stats,
                    'rating' => round($rating, 2),
                    'acciones' => $actions,
                ];
            }
        }

        return Inertia::render('Rendimiento', [
            'players' => array_values($players),
            'statMeta' => (object) $statMeta,
            'gamesCount' => $games->count(),
        ]);
    }

    public function create()
    {
        $players = auth()->check()
            ? auth()->user()->players
            : collect([]);

        $teams = auth()->check()
            ? auth()->user()->teams()->with('players')->orderBy('name')->get()
            : collect([]);

        return Inertia::render('Games/Create', [
            'players' => $players,
            'teams' => $teams,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'team_a' => 'required|array|min:1',
            'team_b' => 'required|array|min:1',
            'team_a_name' => 'nullable|string|max:255',
            'team_b_name' => 'nullable|string|max:255',
        ]);

        $game = Game::create([
            'user_id' => auth()->id(),
            'token' => auth()->check() ? null : \Illuminate\Support\Str::random(32),
            'team_a_name' => $request->filled('team_a_name') ? trim($request->team_a_name) : null,
            'team_b_name' => $request->filled('team_b_name') ? trim($request->team_b_name) : null,
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
        
        $teamA = $game->gamePlayers()->where('team', 'a')->with(['player', 'stats'])->get();
        $teamB = $game->gamePlayers()->where('team', 'b')->with(['player', 'stats'])->get();
        $stats = \App\Models\Stat::where('is_global', true)->with('category')->get();

        $history = \App\Models\StatEvent::where('game_id', $game->id)
            ->with(['gamePlayer.player', 'stat'])
            ->orderBy('id')
            ->get()
            ->map(fn ($event) => [
                'player' => $event->gamePlayer->player->name,
                'stat' => $event->stat->name,
                'team' => $event->gamePlayer->team,
                'gamePlayerId' => $event->game_player_id,
                'statId' => $event->stat_id,
            ]);

        return Inertia::render('Games/Show', [
            'game' => $game,
            'teamA' => $teamA,
            'teamB' => $teamB,
            'stats' => $stats,
            'history' => $history,
        ]);
    }

    public function addStat(Request $request, Game $game)
    {
        if ($game->isFinished()) {
            abort(403, 'El partido está finalizado.');
        }

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

            // Registrar el evento para el historial (solo las sumas)
            \App\Models\StatEvent::create([
                'game_id' => $game->id,
                'game_player_id' => $request->game_player_id,
                'stat_id' => $request->stat_id,
            ]);
        } else {
            if ($gameStat->value > 0) {
                $gameStat->decrement('value');

                // Quitar del historial el último evento de esa stat para ese jugador
                \App\Models\StatEvent::where('game_id', $game->id)
                    ->where('game_player_id', $request->game_player_id)
                    ->where('stat_id', $request->stat_id)
                    ->latest('id')
                    ->first()?->delete();
            }
        }

        // Actualizar marcador si la stat es un gol (propio o en contra)
        $stat = \App\Models\Stat::find($request->stat_id);
        $statName = strtolower($stat->name);

        if ($statName === 'gol' || $statName === 'gol en contra') {
            $gamePlayer = GamePlayer::find($request->game_player_id);

            // Gol normal: suma a tu equipo. Gol en contra: suma al rival.
            $scoringTeam = $statName === 'gol en contra'
                ? ($gamePlayer->team === 'a' ? 'b' : 'a')
                : $gamePlayer->team;

            $column = $scoringTeam === 'a' ? 'score_team_a' : 'score_team_b';

            $request->action === 'add' ? $game->increment($column) : $game->decrement($column);
        }

        return response()->json([
            'gameStat' => $gameStat,
            'game' => $game->fresh(),
        ]);
    }

    public function finish(Game $game)
    {
        $game->update(['finished_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function summary(Game $game)
    {
        $teamA = $game->gamePlayers()->where('team', 'a')->with(['player', 'stats.stat.category'])->get();
        $teamB = $game->gamePlayers()->where('team', 'b')->with(['player', 'stats.stat.category'])->get();

        $history = \App\Models\StatEvent::where('game_id', $game->id)
            ->with(['gamePlayer.player', 'stat'])
            ->orderBy('id')
            ->get()
            ->map(fn ($event) => [
                'player' => $event->gamePlayer->player->name,
                'stat' => $event->stat->name,
                'team' => $event->gamePlayer->team,
                'gamePlayerId' => $event->game_player_id,
                'statId' => $event->stat_id,
            ]);

        return Inertia::render('Games/Summary', [
            'game' => $game,
            'teamA' => $teamA,
            'teamB' => $teamB,
            'history' => $history,
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
