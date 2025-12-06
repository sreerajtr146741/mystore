<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($search = $request->get('q')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function suspend($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->email === 'admin@store.com') {
            return back()->with('error', 'Cannot suspend admin account');
        }

        $user->update(['status' => 'suspended']);
        
        return back()->with('success', 'User suspended successfully');
    }

    public function unsuspend($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);
        
        return back()->with('success', 'User reactivated successfully');
    }

    public function block($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->email === 'admin@store.com') {
            return back()->with('error', 'Cannot block admin account');
        }

        $user->update(['status' => 'blocked']);
        
        return back()->with('success', 'User blocked successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->email === 'admin@store.com') {
            return back()->with('error', 'Cannot delete admin account');
        }

        $user->delete();
        
        return back()->with('success', 'User deleted successfully');
    }
}
