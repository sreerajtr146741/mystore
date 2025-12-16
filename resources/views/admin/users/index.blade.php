<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>User Management • Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @include('partials.premium-styles')
</head>
<body>

<nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button class="btn btn-warning btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>
</nav>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-people me-2"></i>User Management</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card bg-dark border-secondary mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="q" class="form-control" placeholder="Search by name or email" value="{{ request('q') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="buyer" {{ request('role') == 'buyer' ? 'selected' : '' }}>Buyer</option>
                        <option value="seller" {{ request('role') == 'seller' ? 'selected' : '' }}>Seller</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card bg-dark border-secondary">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="user-rows">
                    @include('admin.users.partials.row', ['users' => $users])
                </tbody>
            </table>
        </div>
    </div>

    <!-- Infinite Scroll Elements -->
    @if($users->hasMorePages())
        <div id="loading-spinner" class="text-center py-4 d-none">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
        <div id="sentinel" style="height:20px;"></div>
        <div id="pagination-data" data-next-url="{{ $users->nextPageUrl() }}" style="display:none;"></div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    let nextUrl = document.getElementById('pagination-data')?.dataset.nextUrl;
    const sentinel = document.getElementById('sentinel');
    const spinner = document.getElementById('loading-spinner');
    const container = document.getElementById('user-rows');
    let isLoading = false;

    if (sentinel && nextUrl) {
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !isLoading && nextUrl) {
                loadMore();
            }
        }, { rootMargin: '200px' });
        observer.observe(sentinel);

        function loadMore() {
            isLoading = true;
            spinner.classList.remove('d-none');
            fetch(nextUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                spinner.classList.add('d-none');
                if (html.trim()) {
                    container.insertAdjacentHTML('beforeend', html);
                    const currentUrl = new URL(nextUrl);
                    const p = parseInt(currentUrl.searchParams.get('page')||1) + 1;
                    currentUrl.searchParams.set('page', p);
                    nextUrl = currentUrl.toString();
                    isLoading = false;
                } else {
                    observer.disconnect();
                    sentinel.remove();
                }
            })
            .catch(()=> { spinner.classList.add('d-none'); isLoading = false; });
        }
    }
});
</script>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
