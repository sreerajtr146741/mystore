@forelse($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td><span class="badge bg-info">{{ ucfirst($user->role) }}</span></td>
        <td>
            @if($user->status == 'active')
                <span class="badge badge-active">Active</span>
            @elseif($user->status == 'suspended')
                <span class="badge badge-suspended">Suspended</span>
            @else
                <span class="badge badge-blocked">Blocked</span>
            @endif
        </td>
        <td>{{ $user->created_at->format('M d, Y') }}</td>
        <td class="text-end">
            @if($user->email !== 'admin@store.com')
                <div class="btn-group btn-group-sm">
                    <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button class="btn {{ $user->status == 'active' ? 'btn-outline-warning' : 'btn-outline-success' }} btn-sm" 
                                title="{{ $user->status == 'active' ? 'Deactivate User' : 'Activate User' }}">
                            <i class="bi bi-power"></i>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-dark btn-sm" title="Delete" onclick="return confirm('Delete this user permanently?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            @else
                <span class="text-muted">Protected</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-4">No users found</td>
    </tr>
@endforelse
