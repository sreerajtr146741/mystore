<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Users list for Admin & Seller.
     * - Seller: read-only
     * - Admin: can manage users
     */
    public function index(Request $request)
    {
        try {

            $q      = trim($request->get('q', ''));
            $role   = $request->get('role', '');
            $status = $request->get('status', '');

            $users = User::query()
                ->when($q, function($qr) use ($q) {
                    $qr->where(function($w) use ($q){
                        $w->where('id', $q)
                          ->orWhere('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%")
                          ->orWhere('phone', 'like', "%{$q}%");
                    });
                })
                ->when($role, fn($qr) => $qr->where('role', $role))
                ->when($status, fn($qr) => $qr->where('status', $status))
                ->orderByDesc('id')
                ->paginate(12)
                ->withQueryString();

            $isAdmin = method_exists($request->user(), 'isAdmin') && $request->user()->isAdmin();

            return view('admin.users.index', compact('users','q','role','status','isAdmin'));

        } catch (\Throwable $e) {

            \Log::error('UserController index error: '.$e->getMessage());
            return back()->with('error', 'Unable to load users list.');
        }
    }

    /**
     * Admin: change status to active/suspended.
     */
    public function updateStatus(Request $request, User $user)
    {
        try {

            $this->authorizeAdmin($request);

            $request->validate([
                'status' => 'required|in:active,suspended,pending'
            ]);

            if ($request->user()->id === $user->id && $request->input('status') !== 'active') {
                return back()->withErrors('You cannot suspend your own account.');
            }

            $user->status = $request->input('status');
            $user->save();

            return back()->with('success', "User #{$user->id} status updated to {$user->status}.");

        } catch (\Throwable $e) {

            \Log::error('UserController updateStatus error: '.$e->getMessage());
            return back()->with('error', 'Unable to update user status.');
        }
    }

    /**
     * Admin: delete user.
     */
    public function destroy(Request $request, User $user)
    {
        try {

            $this->authorizeAdmin($request);

            if ($request->user()->id === $user->id) {
                return back()->withErrors('You cannot delete your own account.');
            }

            $user->delete();

            return back()->with('success', "User #{$user->id} deleted.");

        } catch (\Throwable $e) {

            \Log::error('UserController destroy error: '.$e->getMessage());
            return back()->with('error', 'Unable to delete user.');
        }
    }

    private function authorizeAdmin(Request $request): void
    {
        if (!(method_exists($request->user(), 'isAdmin') && $request->user()->isAdmin())) {
            abort(403, 'Only admin can perform this action.');
        }
    }
}
