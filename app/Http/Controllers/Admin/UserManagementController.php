<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('sellerApplication')->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function updateRole(User $user, Request $request)
    {
        $request->validate(['role' => 'required|in:user,seller,admin']);
        $user->update(['role' => $request->role]);

        return back()->with('success', 'User role updated to ' . $request->role);
    }
}