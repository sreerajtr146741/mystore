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

        if ($request->ajax()) {
            return view('admin.users.partials.row', compact('users'))->render();
        }

        return view('admin.users.index', compact('users'));
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->email === 'admin@store.com') {
            return back()->with('error', 'Cannot change status of admin account');
        }

        // Toggle logic: If active, suspend. If suspended/blocked, activate.
        if ($user->status === 'active') {
            $user->update(['status' => 'suspended']);
            $msg = 'User deactivated (suspended) successfully';
        } else {
            $user->update(['status' => 'active']);
            $msg = 'User activated successfully';
        }
        
        return back()->with('success', $msg);
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
