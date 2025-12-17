{{-- resources/views/dashboard/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard • MyStore</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    <style>
        body { margin:0; background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#1e40af 100%); min-height:100vh; overflow-x:hidden; font-family:'Segoe UI',sans-serif; }
        .text-gradient{ background:linear-gradient(90deg,#00d4ff,#ff00c8); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
        .card-glass{ background:rgba(255,255,255,0.08); backdrop-filter:blur(16px); border:1px solid rgba(255,255,255,0.15); border-radius:20px; }
        .btn-glass{ background:rgba(255,255,255,0.1); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.2); }
        .hover-lift:hover{ transform:translateY(-15px) scale(1.02); box-shadow:0 30px 60px rgba(0,0,0,0.5)!important; }
        .btn-3d:hover{ transform:translateY(-8px) scale(1.05); box-shadow:0 20px 40px rgba(0,0,0,0.4)!important; }
        .btn-cyan{ background:linear-gradient(135deg,#06b6d4,#0891b2); }
        .btn-indigo{ background:linear-gradient(135deg,#6366f1,#4f46e5); }
        .bg-gradient-green{ background:linear-gradient(135deg,#10b981,#059669); }
        .bg-gradient-warning{ background:linear-gradient(135deg,#fbbf24,#f59e0b); }
        .animate-float{ animation:float 20s infinite linear; position:absolute; }
        @keyframes float{ 0%{transform:translateY(100vh) rotate(0deg); opacity:0;} 10%{opacity:1;} 90%{opacity:1;} 100%{transform:translateY(-100px) rotate(360deg); opacity:0;} }
        .table-glass thead th { color:#cbd5e1; }
        .table-glass tbody td { color:#e2e8f0; }
    </style>
</head>
<body>

{{-- FLOATING ELEMENTS --}}
<div class="position-absolute top-0 start-0 w-100 h-100 opacity-10 pointer-events-none">
    <div class="animate-float" style="left:10%; top:20%;"><i class="bi bi-stars text-info" style="font-size:4rem;"></i></div>
    <div class="animate-float" style="left:80%; top:30%; animation-delay:5s;"><i class="bi bi-lightning-charge-fill text-warning" style="font-size:3.5rem;"></i></div>
    <div class="animate-float" style="left:50%; top:10%; animation-delay:10s;"><i class="bi bi-rocket-takeoff-fill text-success" style="font-size:5rem;"></i></div>
</div>

{{-- TOP NAV --}}
<div class="position-absolute top-0 end-0 p-4 z-50">
    <div class="dropdown">
        <button class="btn btn-glass dropdown-toggle d-flex align-items-center gap-3 shadow-lg" type="button" data-bs-toggle="dropdown">
            <img src="{{ auth()->user()->profile_photo_url ?? asset('images/avatar.png') }}" class="rounded-circle border border-white border-3" width="50" height="50">
            <div class="text-start">
                <div class="fw-bold text-white">{{ auth()->user()->name }}</div>
                <small class="text-info text-capitalize">{{ auth()->user()->role }}</small>
            </div>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
            <li><a href="{{ route('profile.edit') }}" class="dropdown-item py-3 d-flex align-items-center gap-3"><i class="bi bi-person-circle fs-4 text-info"></i> My Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="dropdown-item py-3 text-danger d-flex align-items-center gap-3">
                        <i class="bi bi-box-arrow-right fs-4"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>

<div class="container py-6 pt-20 position-relative">

    {{-- WELCOME --}}
    <div class="text-center text-white mb-5">
        <h1 class="display-2 fw-bold mb-3">
            Welcome back, <span class="text-gradient">{{ auth()->user()->name }}</span>!
        </h1>
        <p class="lead fs-2">You’re in the <span class="text-info fw-bold"><i class="bi bi-bag-fill me-2"></i>MyStore Control Room</span></p>
    </div>

    {{-- ADMIN PANEL --}}
    @if(auth()->user()->isAdmin())
    <div class="mb-5">
        <h2 class="text-white mb-3"><i class="bi bi-shield-lock-fill me-2"></i>Admin Dashboard</h2>
        <div class="row g-4 mb-4">
            @php
                $cards = [
                    ['icon'=>'people-fill','title'=>'Total Users','value'=>$stats['total_users'] ?? 0,'growth'=>'+12%'],
                    ['icon'=>'box-seam-fill','title'=>'Total Products','value'=>$stats['total_products'] ?? 0,'extra'=>($stats['new_today'] ?? 0).' new today'],
                    ['icon'=>'shop-window','title'=>'Active Sellers','value'=>$stats['sellers'] ?? 0,'extra'=>'Verified & Active'],
                    ['icon'=>'currency-rupee','title'=>"Today's Revenue",'value'=>'₹'.number_format($stats['today_revenue'] ?? 0),'growth'=>'+28%'],
                ];
            @endphp
            @foreach($cards as $card)
                <div class="col-md-6 col-lg-3">
                    <div class="card-glass hover-lift p-4 text-white">
                        <div class="d-flex justify-content-between mb-2">
                            <i class="bi bi-{{ $card['icon'] }} fs-1 opacity-50"></i>
                        </div>
                        <div class="opacity-75">{{ $card['title'] }}</div>
                        <h2 class="fw-bold">{{ $card['value'] }}</h2>
                        @if(isset($card['growth']))
                            <small class="text-success"><i class="bi bi-graph-up-arrow"></i> {{ $card['growth'] }}</small>
                        @else
                            <small class="text-info"><i class="bi bi-plus-circle"></i> {{ $card['extra'] }}</small>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mb-5">
            <h3 class="text-white mb-4">Control Center</h3>
            <div class="d-flex flex-wrap justify-content-center gap-4">
                <a href="{{ route('admin.users') }}" class="btn btn-cyan btn-3d text-white px-5 py-4 rounded-pill">
                    <i class="bi bi-people-fill fs-2"></i><br>Manage Users
                </a>
                <a href="{{ route('admin.products') }}" class="btn btn-indigo btn-3d text-white px-5 py-4 rounded-pill">
                    <i class="bi bi-grid-3x3-gap-fill fs-2"></i><br>All Products
                </a>
                <a href="{{ route('admin.seller-applications') }}" class="btn btn-warning btn-3d text-dark px-5 py-4 rounded-pill">
                    <i class="bi bi-person-check-fill fs-2"></i><br>Review Sellers
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- SELLER PANEL --}}
    @if(auth()->user()->isSeller())
    <div class="mb-5">
        <h2 class="text-white mb-3"><i class="bi bi-bag-check-fill me-2"></i>Seller Panel</h2>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card-glass p-4 text-white">
                    <div class="opacity-75">My Products</div>
                    <h2 class="fw-bold">{{ $sellerStats['count'] ?? 0 }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-glass p-4 text-white">
                    <div class="opacity-75">Total Value</div>
                    <h2 class="fw-bold">₹{{ number_format($sellerStats['total_value'] ?? 0) }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-glass p-4 text-white">
                    <div class="opacity-75">Low Stock (≤5)</div>
                    <h2 class="fw-bold">{{ $sellerStats['low_stock'] ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-white mb-0">My Products</h4>
            <button class="btn btn-success btn-3d" data-bs-toggle="modal" data-bs-target="#createProductModal">
                <i class="bi bi-plus-circle"></i> Add Product
            </button>
        </div>

        <div class="card-glass p-3">
            <div class="table-responsive">
                <table class="table table-borderless align-middle table-glass mb-0">
                    <thead>
                        <tr>
                            <th>#</th><th>Name</th><th>Price (₹)</th><th>Stock</th><th>Updated</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sellerProducts as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->name }}</td>
                            <td>{{ number_format($p->price,2) }}</td>
                            <td>{{ $p->stock }}</td>
                            <td>{{ $p->updated_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#editProductModal"
                                    data-id="{{ $p->id }}" data-name="{{ $p->name }}"
                                    data-price="{{ $p->price }}" data-stock="{{ $p->stock }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <form action="{{ route('seller.products.destroy', $p->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">
                                        <i class="bi bi-trash3"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-white-50 py-4">No products yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Create Modal --}}
        <div class="modal fade" id="createProductModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('seller.products.store') }}">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title">Add Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Name</label>
                            <input name="name" class="form-control" required>
                        </div>
                        <div class="mb-3"><label class="form-label">Price (₹)</label>
                            <input name="price" type="number" step="0.01" min="0" class="form-control" required>
                        </div>
                        <div class="mb-3"><label class="form-label">Stock</label>
                            <input name="stock" type="number" min="0" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-success">Create</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit Modal --}}
        <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editProductForm" class="modal-content" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header"><h5 class="modal-title">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Name</label>
                            <input id="edit-name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3"><label class="form-label">Price (₹)</label>
                            <input id="edit-price" name="price" type="number" step="0.01" min="0" class="form-control" required>
                        </div>
                        <div class="mb-3"><label class="form-label">Stock</label>
                            <input id="edit-stock" name="stock" type="number" min="0" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    @endif

    {{-- FOOTER --}}
    <div class="text-center text-white-50 pb-4">
        <p class="mb-0 fs-5">
            <i class="bi bi-clock-history"></i>
            Updated: {{ now()->format('d M Y • h:i A') }} • MyStore Dashboard v2.0
        </p>
        @if(session('success'))
            <p class="text-success mt-2">{{ session('success') }}</p>
        @endif
        @if($errors->any())
            <p class="text-warning mt-2">{{ $errors->first() }}</p>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const editModal = document.getElementById('editProductModal');
editModal?.addEventListener('show.bs.modal', event => {
    const btn = event.relatedTarget;
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    const price = btn.getAttribute('data-price');
    const stock = btn.getAttribute('data-stock');

    document.getElementById('edit-name').value = name;
    document.getElementById('edit-price').value = price;
    document.getElementById('edit-stock').value = stock;

    const form = document.getElementById('editProductForm');
    form.action = `{{ url('seller/products') }}/${id}`;
});
</script>
</body>
</html>
