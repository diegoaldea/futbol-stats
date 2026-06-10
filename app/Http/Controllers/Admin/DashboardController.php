<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Stat;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'counts' => [
                'users' => User::count(),
                'games' => Game::count(),
                'globalStats' => Stat::where('is_global', true)->count(),
            ],
        ]);
    }
}
