<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserManagementController extends Controller
{
    public function index()
    {
        return response()->json(['status' => true, 'data' => User::all()]);
    }

    public function show($id)
    {
        return response()->json(['status' => true, 'data' => User::find($id)]);
    }

    public function toggleStatus($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->status = ($user->status === 'active' ? 'blocked' : 'active');
            $user->save();
        }
        return response()->json(['status' => true, 'message' => 'Status updated']);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['status' => true, 'message' => 'User deleted']);
    }
}
