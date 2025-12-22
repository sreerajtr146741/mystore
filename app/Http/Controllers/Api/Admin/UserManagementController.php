<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * List all users with filters
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter byrole
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate($request->get('per_page', 15));

        return ApiResponse::success($users);
    }

    /**
     * Get user details
     */
    public function show($id)
    {
        $user = User::with('orders')->find($id);

        if (!$user) {
            return ApiResponse::notFound('User not found');
        }

        return ApiResponse::success(['user' => $user]);
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,blocked'
        ]);

        $user = User::find($id);

        if (!$user) {
            return ApiResponse::notFound('User not found');
        }

        if ($user->role === 'admin') {
            return ApiResponse::error('Cannot modify admin user status', 403);
        }

        $user->update(['status' => $request->status]);

        return ApiResponse::success(
            ['user' => $user],
            'User status updated successfully'
        );
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return ApiResponse::notFound('User not found');
        }

        if ($user->role === 'admin') {
            return ApiResponse::error('Cannot delete admin user', 403);
        }

        $user->delete();

        return ApiResponse::success(null, 'User deleted successfully');
    }
}
