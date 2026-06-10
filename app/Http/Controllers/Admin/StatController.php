<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stat;
use App\Models\StatCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Stats', [
            'stats' => Stat::where('is_global', true)->with('category')->latest()->get(),
            'categories' => StatCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateStat($request);

        Stat::create([
            ...$data,
            'is_global' => true,
            'user_id' => null,
        ]);

        return back();
    }

    public function update(Request $request, Stat $stat)
    {
        $stat->update($this->validateStat($request));

        return back();
    }

    public function destroy(Stat $stat)
    {
        $stat->delete();

        return back();
    }

    private function validateStat(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'stat_category_id' => 'required|exists:stat_categories,id',
            'points' => 'required|numeric',
        ]);
    }
}
