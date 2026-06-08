<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

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
        
    }

    public function show(string $id)
    {
        
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
