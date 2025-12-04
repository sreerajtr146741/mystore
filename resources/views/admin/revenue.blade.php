<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Revenue Details • Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #121721;
      color: #e2e8f0;
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
    }
    .navbar {
      background: #0d1117 !important;
      border-bottom: 1px solid #30363d;
    }
    .card {
      background: #161b22;
      border: 1px solid #30363d;
      border-radius: 12px;
      transition: all 0.3s ease;
    }
    .card:hover {
      border-color: #58a6ff;
      box-shadow: 0 8px 25px rgba(88, 166, 255, 0.15);
      transform: translateY(-4px);
    }
    .list-group-item {
      background: #161b22;
      border-color: #30363d;
      color: #e2e8f0;
      padding: 1.25rem 1.5rem;
      border-radius: 10px !important;
      margin-bottom: 12px;
    }
    .amount {
      font-weight: 700;
      font-size: 1.5rem;
      color: #58a6ff;
    }
    .btn-logout {
      background: #f85149;
      border: none;
      border-radius: 8px;
      padding: 8px 20px;
      font-weight: 600;
    }
    .btn-logout:hover { background: #da3633; }
    .btn-back {
      background: transparent;
      border: 2px solid #58a6ff;
      color: #58a6ff;
      border-radius: 10px;
      padding: 10px 30px;
      font-weight: 600;
      transition: all 0.3s;
    }
    .btn-back:hover {
      background: #58a6ff;
      color: white;
    }
    h2 {
      color: #f0f6fc;
      font-weight: 700;
    }

    /* THIS LINE NOW STANDS OUT BEAUTIFULLY */
    .highlight-subtitle {
      font-size: 1.25rem;
      font-weight: 600;
      background: linear-gradient(90deg, #8b5cf6, #3b82f6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      letter-spacing: 0.5px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-dark">
  <div class="container d-flex justify-content-between align-items-center">
    <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">MyStore Admin</a>
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button class="btn btn-logout text-white">Logout</button>
    </form>
  </div>
</nav>

<div class="container py-5">
  <div class="text-center mb-5">
    <h2>Revenue Details</h2>
    <p class="highlight-subtitle mt-3">
      Today's earnings and performance summary
    </p>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="list-group list-group-flush">
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>Today</div>
            <span class="amount">₹{{ number_format($revenue['today'] ?? 0) }}</span>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>This Week</div>
            <span class="amount">₹{{ number_format($revenue['week'] ?? 0) }}</span>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>This Month</div>
            <span class="amount">₹{{ number_format($revenue['month'] ?? 0) }}</span>
          </div>
        </div>
      </div>

      <div class="text-center mt-4">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-back">
          Back to Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>