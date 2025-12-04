{{-- resources/views/admin/users/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Users • MyStore</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* ============================
       GLOBAL DARK THEME
    ============================ */
    body {
      background:#0d1117;          /* Dark theme */
      color:#e6edf3;
      font-family:'Segoe UI',sans-serif;
    }

    nav.navbar {
      background:#0a0f16 !important;
      border-bottom:1px solid #1f2937;
    }

    .navbar-brand,
    .navbar-text,
    .btn,
    h2,
    h1,
    h3,
    h4,
    h5,
    h6 {
      color:#e6edf3 !important;
    }

    /* ============================
       FILTER CARD
    ============================ */
    .card-glass {
      background:#161b22;
      border:1px solid #2d333b;
      border-radius:16px;
    }

    /* ===== Simple Visible Search Bar Text ===== */
    .form-control,
    .form-select {
      background:#0d1117;
      color:#e6edf3 !important;           /* Simple readable text */
      border:1px solid #30363d;
    }

    .form-control::placeholder {
      color:#cfd6dd !important;           /* Simple light colour */
    }

    .form-control:focus,
    .form-select:focus {
      background:#0d1117;
      color:#ffffff;
      border-color:#58a6ff;
      box-shadow:none;
    }

    /* ============================
       AVATAR
    ============================ */
    .avatar {
      width:36px;height:36px;border-radius:50%;
      display:flex;align-items:center;justify-content:center;
      font-weight:700;color:white;border:2px solid #fff;
    }

    /* ============================
       DARK TABLE
    ============================ */
    .table {
      background:#161b22;
      color:#e6edf3;
      border:1px solid #30363d;
      border-radius:10px;
      overflow:hidden;
    }

    .table thead th {
      background:#1f2630;
      color:#e6edf3;
      border-bottom:2px solid #30363d;
      font-weight:600;
    }

    .table tbody td {
      background:#161b22;
      color:#e6edf3;
      border-color:#30363d;
      vertical-align:middle;
    }

    .table-hover tbody tr:hover {
      background:#1f2630 !important;
    }

    /* ============================
       BADGES (Readable Colors)
    ============================ */
    .badge-role {
      font-weight:600;
      padding:4px 8px;
      letter-spacing:.3px;
    }

    .bg-primary   { background:#1f6feb !important; color:white !important; }
    .bg-success   { background:#238636 !important; color:white !important; }
    .bg-warning   { background:#f0ad4e !important; color:black !important; }
    .bg-danger    { background:#da3633 !important; color:white !important; }
    .bg-info      { background:#58a6ff !important; color:black !important; }
    .bg-secondary { background:#6e7681 !important; color:white !important; }

    /* ============================
       BUTTONS DARK OUTLINE
    ============================ */
    .btn-outline-dark,
    .btn-outline-primary,
    .btn-outline-success,
    .btn-outline-warning,
    .btn-outline-danger {
      border-color:#e6edf3 !important;
      color:#e6edf3 !important;
    }

    .btn-outline-dark:hover,
    .btn-outline-primary:hover,
    .btn-outline-success:hover,
    .btn-outline-warning:hover,
    .btn-outline-danger:hover {
      background:#e6edf3 !important;
      color:#000 !important;
    }

  </style>
</head>
<body>

<nav class="navbar navbar-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ url('/dashboard') }}">MyStore</a>
    <span class="navbar-text">Users</span>

    <div class="d-flex align-items-center gap-2">
      <a href="{{ url('/dashboard') }}" class="btn btn-outline-light btn-sm">
        <i class="bi bi-speedometer2 me-1"></i>Dashboard
      </a>

      <form action="{{ route('logout') }}" method="POST" class="m-0">
        @csrf
        <button class="btn btn-warning btn-sm">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </button>
      </form>
    </div>
  </div>
</nav>

<main class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Users</h2>
    <div class="text-white-50 small">
      {{ $isAdmin ? 'Admin View' : 'Read-only View' }}
    </div>
  </div>

  {{-- FILTERS --}}
  <form method="GET" class="card-glass p-3 mb-3">
    <div class="row g-2">

      <div class="col-md-4">
        <input type="text" name="q" value="{{ $q }}"
               class="form-control"
               placeholder="Search by ID / Name / Email / Phone">
      </div>

      <div class="col-md-3">
        <select name="role" class="form-select">
          <option value="">All Roles</option>
          <option value="buyer"  {{ $role==='buyer' ? 'selected' : '' }}>Buyer</option>
          <option value="seller" {{ $role==='seller'? 'selected' : '' }}>Seller</option>
          <option value="admin"  {{ $role==='admin' ? 'selected' : '' }}>Admin</option>
        </select>
      </div>

      <div class="col-md-3">
        <select name="status" class="form-select">
          <option value="">All Status</option>
          <option value="active"    {{ $status==='active'    ? 'selected' : '' }}>Active</option>
          <option value="suspended" {{ $status==='suspended' ? 'selected' : '' }}>Suspended</option>
          <option value="pending"   {{ $status==='pending'   ? 'selected' : '' }}>Pending</option>
        </select>
      </div>

      <div class="col-md-2 d-grid">
        <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
      </div>

    </div>
  </form>

  {{-- ============================
       USERS TABLE
  ============================ --}}
  <div class="card-glass p-0 mb-3">
    <div class="table-responsive">

      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>User ID</th>
            <th></th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Status</th>
            <th>Activity</th>
            @if($isAdmin)
              <th class="text-end">Actions</th>
            @endif
          </tr>
        </thead>

        <tbody>
        @forelse($users as $u)
          @php
            $name  = trim($u->name ?? '—');
            $photo = $u->profile_photo_url ?? $u->profile_photo ?? null;
            $initials = collect(explode(' ', $name))->map(fn($p)=>mb_substr($p,0,1))->take(2)->implode('');
            $colors = ['#0ea5e9','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444','#14b8a6','#84cc16'];
            $color = $colors[(crc32($name) % count($colors))];
          @endphp

          <tr>
            <td class="text-light">#{{ $u->id }}</td>

            <td>
              @if($photo)
                <img src="{{ $photo }}" class="avatar">
              @else
                <span class="avatar" style="background:{{ $color }}">{{ $initials }}</span>
              @endif
            </td>

            <td class="text-light">{{ $u->name }}</td>
            <td class="text-light">{{ $u->email }}</td>
            <td class="text-light">{{ $u->phone ?? '—' }}</td>

            <td><span class="badge badge-role bg-primary">{{ $u->role }}</span></td>
            <td><span class="badge badge-role bg-success">{{ ucfirst($u->status) }}</span></td>

            <td class="small text-light">
              <div><i class="bi bi-clock-history me-1"></i>
                {{ $u->last_login_at ? \Carbon\Carbon::parse($u->last_login_at)->format('d M Y, h:i A') : '—' }}
              </div>
              <div><i class="bi bi-calendar-event me-1"></i>
                {{ $u->created_at->format('d M Y') }}
              </div>
            </td>

            @if($isAdmin)
            <td class="text-end">
              <a href="{{ route('profile.edit') }}?user={{ $u->id }}" class="btn btn-outline-dark btn-sm btn-icon">
                <i class="bi bi-person-badge"></i>
              </a>

              <a href="{{ route('profile.edit') }}?user={{ $u->id }}&mode=edit" class="btn btn-outline-primary btn-sm btn-icon">
                <i class="bi bi-pencil-square"></i>
              </a>

              <form action="{{ route('admin.users.status', $u) }}" method="POST" class="d-inline">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="suspended">
                <button class="btn btn-outline-warning btn-sm btn-icon" onclick="return confirm('Suspend user?')">
                  <i class="bi bi-ban"></i>
                </button>
              </form>

              <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm btn-icon" onclick="return confirm('Delete user?')">
                  <i class="bi bi-trash3"></i>
                </button>
              </form>
            </td>
            @endif

          </tr>

        @empty
          <tr>
            <td colspan="{{ $isAdmin ? 9 : 8 }}" class="text-center py-5 text-light">
              <i class="bi bi-people fs-3 d-block mb-2"></i>No users found.
            </td>
          </tr>
        @endforelse
        </tbody>

      </table>

    </div>
  </div>

  {{ $users->links() }}

  @if(session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-warning mt-3">{{ $errors->first() }}</div>
  @endif

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
