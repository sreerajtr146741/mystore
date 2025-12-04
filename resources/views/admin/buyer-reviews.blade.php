<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Review Buyers • Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="{{ route('admin.dashboard') }}">MyStore Admin</a>
    <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf
      <button class="btn btn-warning btn-sm">Logout</button>
    </form>
  </div>
</nav>

<div class="container py-4">
  <h2 class="mb-3">Review Buyers</h2>
  <p class="text-muted">Approve/flag buyers based on disputes, refunds, or suspicious activity.</p>

  <div class="table-responsive">
    <table class="table table-bordered align-middle bg-white">
      <thead class="table-light">
        <tr><th>#</th><th>Name</th><th>Email</th><th>Orders</th><th>Flags</th><th>Action</th></tr>
      </thead>
      <tbody>
        @forelse($buyers as $b)
          <tr>
            <td>{{ $b['id'] }}</td>
            <td>{{ $b['name'] }}</td>
            <td>{{ $b['email'] }}</td>
            <td>{{ $b['orders'] }}</td>
            <td>{{ $b['flags'] }}</td>
            <td>
              <button class="btn btn-sm btn-outline-danger">Flag</button>
              <button class="btn btn-sm btn-outline-success">Approve</button>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted">No buyers to review.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary mt-3">Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
