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

    public function show($id)
    {
        $user = User::withCount('products')->findOrFail($id);
        
         // Calculate total spent if orders relationship exists, else 0
        // Provided generic stats
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? 'N/A',
            'address' => $user->address ?? 'N/A',
            'role' => ucfirst($user->role),
            'status' => ucfirst($user->status),
            'joined' => $user->created_at->format('d M Y, h:i A'),
            'avatar' => $user->profile_photo_url,
            'orders_count' => 0, // Placeholder until orders relationship is confirmed
            // If user has orders relation: $user->orders()->count()
        ]);
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
