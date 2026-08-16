<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $users = User::all();
        $roles = Role::all();
        return view('users.index', compact('users','roles'));
    }

    public function updateRoles(Request $request, User $user)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'roles' => 'array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $user->syncRoles($request->roles ?? []);

        return back()->with('success','Kullanıcı rolleri güncellendi.');
    }
}
