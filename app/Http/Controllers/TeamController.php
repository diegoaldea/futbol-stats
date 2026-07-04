<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'player_ids' => 'required|array|min:1',
            'player_ids.*' => [Rule::exists('players', 'id')->where('user_id', auth()->id())],
        ]);

        // Avisar y no guardar si ya existe un equipo con ese nombre para este usuario
        $exists = auth()->user()->teams()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($request->name))])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya tenés un equipo con ese nombre.',
            ], 422);
        }

        $team = auth()->user()->teams()->create([
            'name' => trim($request->name),
        ]);

        $team->players()->sync($request->player_ids);

        return response()->json($team->load('players'));
    }
}
