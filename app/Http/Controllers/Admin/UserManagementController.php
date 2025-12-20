<?php
namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserManagementController extends Controller
{
    public function index()
    {
        try {

            $users = User::with('sellerApplication')->latest()->paginate(20);
            return view('admin.users', compact('users'));

        } catch (\Throwable $e) {

            \Log::error('UserManagement index error: '.$e->getMessage());
            return back()->with('error', 'Unable to load users.');
        }
    }

    public function updateRole(User $user, Request $request)
    {
        try {

            $request->validate(['role' => 'required|in:user,seller,admin']);

            $user->update(['role' => $request->role]);

            return back()->with('success', 'User role updated to ' . $request->role);

        } catch (\Throwable $e) {

            \Log::error('UserManagement updateRole error: '.$e->getMessage());
            return back()->with('error', 'Unable to update user role.');
        }
    }
}
