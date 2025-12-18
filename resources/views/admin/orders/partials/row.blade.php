@forelse($orders as $order)
    <tr>
        <td class="ps-4 py-3">
            <div class="fw-bold text-dark">#{{ $order->id }}</div>
            <div class="small text-muted mb-1">{{ $order->user->name ?? 'Guest' }}</div>
            <div class="small text-muted">{{ $order->items->count() }} Items</div>
            <div class="d-flex gap-1 mt-1">
                @foreach($order->items->take(4) as $item)
                    @if($item->product && $item->product->image)
                        <img src="{{ asset('storage/'.$item->product->image) }}" 
                                alt="img" 
                                class="rounded border"
                                style="width: 32px; height: 32px; object-fit: cover;"
                                title="{{ $item->product->name }}">
                    @endif
                @endforeach
                @if($order->items->count() > 4)
                    <span class="badge bg-light text-secondary border d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">+{{ $order->items->count() - 4 }}</span>
                @endif
            </div>
        </td>
        <td>
            <div class="small text-muted">Ordered: {{ $order->created_at->format('M d, Y') }}</div>
            @if($order->status == 'delivered')
                <div class="small text-success fw-bold">Delivered: {{ $order->updated_at->format('M d') }}</div>
            @elseif($order->status == 'shipped')
                <div class="small text-primary fw-bold">Shipped: {{ $order->updated_at->format('M d') }}</div>
            @elseif($order->status == 'processing')
                <div class="small text-info fw-bold">Processing: {{ $order->updated_at->format('M d') }}</div>
            @elseif($order->delivery_date)
                    <div class="small text-muted">Exp: {{ $order->delivery_date->format('M d') }}</div>
            @endif
        </td>
        <td>
            @php
                $badge = match($order->status) {
                    'placed' => 'bg-secondary',
                    'processing' => 'bg-info text-dark',
                    'shipped' => 'bg-primary',
                    'out_for_delivery' => 'bg-warning text-dark',
                    'delivered' => 'bg-success',
                    'cancelled' => 'bg-danger',
                    'return_requested' => 'bg-warning text-dark',
                    'returned' => 'bg-secondary',
                    default => 'bg-light text-dark border',
                };
            @endphp
            <span class="badge {{ $badge }} rounded-pill font-monospace fw-normal px-3 py-2">
                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
        </td>
        <td class="fw-bold">₹{{ number_format($order->total, 2) }}</td>
        <td class="text-end pe-4">
            <div class="d-flex justify-content-end gap-2">
                {{-- Quick Action Button --}}
                @if($order->status == 'placed')
                    <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="d-inline status-update-form">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="processing">
                        <button class="btn btn-sm btn-outline-primary" title="Accept Order">
                            Accept
                        </button>
                    </form>
                @elseif($order->status == 'processing')
                    <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="d-inline status-update-form">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="shipped">
                        <button class="btn btn-sm btn-warning text-dark" title="Ship Order">
                            Ship
                        </button>
                    </form>
                @elseif($order->status == 'shipped')
                    <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="d-inline status-update-form">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="delivered">
                        <button class="btn btn-sm btn-success" title="Mark Delivered">
                            Deliver
                        </button>
                    </form>
                @elseif($order->status == 'return_requested')
                    <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="d-inline status-update-form">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="returned">
                        <button class="btn btn-sm btn-success" title="Approve Return">
                            Approve
                        </button>
                    </form>
                @endif

                {{-- Manual Status Override (Dropdown) --}}
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header">Update Status</h6></li>
                        @foreach(['placed','processing','shipped','delivered','cancelled','returned'] as $s)
                            <li>
                                <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="status-update-form">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $s }}">
                                    <button class="dropdown-item {{ $order->status == $s ? 'active' : '' }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</button>
                                </form>
                            </li>
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.show', $order->id) }}"><i class="bi bi-eye me-2"></i>View Details</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.download', $order->id) }}"><i class="bi bi-download me-2"></i>Invoice</a></li>
                    </ul>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-5 text-muted">
            <i class="bi bi-inbox display-6 d-block mb-3"></i>
            No orders found in this category.
        </td>
    </tr>
@endforelse
