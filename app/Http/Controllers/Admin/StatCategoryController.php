<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StatCategory;
use Illuminate\Http\Request;

class StatCategoryController extends Controller
{
    public function store(Request $request)
    {
        StatCategory::create($this->validateCategory($request));

        return back();
    }

    public function update(Request $request, StatCategory $statCategory)
    {
        $statCategory->update($this->validateCategory($request));

        return back();
    }

    public function destroy(StatCategory $statCategory)
    {
        $statCategory->delete();

        return back();
    }

    private function validateCategory(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
    }
}
