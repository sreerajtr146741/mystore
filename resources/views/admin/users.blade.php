@foreach($users as $user)
<tr>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td>
        <form action="{{ route('admin.users.updateRole', $user) }}" method="POST" class="d-inline">
            @csrf @method('PATCH')
            <select name="role" onchange="this.form.submit()" class="form-select form-select-sm">
                <option {{ $user->role == 'user' ? 'selected' : '' }}>user</option>
                <option {{ $user->role == 'seller' ? 'selected' : '' }}>seller</option>
                <option {{ $user->role == 'admin' ? 'selected' : '' }}>admin</option>
            </select>
        </form>
    </td>
</tr>
@endforeach