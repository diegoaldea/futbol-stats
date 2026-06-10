<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users', [
            'users' => User::orderBy('name')->get(['id', 'name', 'email', 'role']),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        // Evitar que el admin se quite a sí mismo el rol y quede sin acceso.
        if ($user->id === $request->user()->id && $request->role !== 'admin') {
            return back()->withErrors(['role' => 'No podés quitarte el rol de admin a vos mismo.']);
        }

        $user->role = $request->role;
        $user->save();

        return back();
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'No podés eliminar tu propia cuenta.']);
        }

        $user->delete();

        return back();
    }
}
