@forelse($orders as $order)
    <tr class="border-white border-opacity-5 hover-bg-glass transition-all">
        <td class="ps-4 py-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-info bg-opacity-20 p-2 px-3 rounded-3 text-white border border-info border-opacity-50 fw-bold font-monospace shadow-sm" style="font-size: 1.1rem; min-width: 80px; text-align: center;">
                    #{{ $order->id }}
                </div>
                <div>
                    <div class="fw-bold text-white mb-0" style="font-size: 1rem;">{{ $order->user->name ?? 'Guest Customer' }}</div>
                    <div class="text-white-50 small d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam"></i> {{ $order->items->count() }} {{ $order->items->count() == 1 ? 'item' : 'items' }}
                    </div>
                    <div class="d-flex gap-1 mt-2">
                        @foreach($order->items->take(4) as $item)
                            @if($item->product && $item->product->image)
                                <img src="{{ asset('storage/'.$item->product->image) }}" 
                                        alt="img" 
                                        class="rounded-2 border border-white border-opacity-20 shadow-sm"
                                        style="width: 28px; height: 28px; object-fit: cover;"
                                        title="{{ $item->product->name }}">
                            @endif
                        @endforeach
                        @if($order->items->count() > 4)
                            <div class="bg-white bg-opacity-10 rounded-2 border border-white border-opacity-20 d-flex align-items-center justify-content-center text-white-50" style="width: 28px; height: 28px; font-size: 0.65rem;">
                                +{{ $order->items->count() - 4 }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </td>
        <td class="py-4">
            <div class="d-flex flex-column gap-1">
                <div class="small text-white-50 d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-event text-info"></i> Placed: {{ $order->created_at->format('M d, Y') }}
                </div>
                @if($order->status == 'delivered')
                    <div class="small text-success fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-check-all"></i> Completed: {{ $order->updated_at->format('M d') }}
                    </div>
                @elseif($order->status == 'shipped')
                    <div class="small text-primary-emphasis fw-bold d-flex align-items-center gap-2" style="color: #38bdf8 !important;">
                        <i class="bi bi-truck"></i> Shipped: {{ $order->updated_at->format('M d') }}
                    </div>
                @elseif($order->status == 'processing')
                    <div class="small text-warning-emphasis fw-bold d-flex align-items-center gap-2" style="color: #fbbf24 !important;">
                        <i class="bi bi-gear-fill"></i> Processing: {{ $order->updated_at->format('M d') }}
                    </div>
                @endif
            </div>
        </td>
        <td class="py-4">
            @php
                $badgeClasses = match($order->status) {
                    'pending' => 'bg-info bg-opacity-25 text-white border-info border-opacity-50',
                    'placed' => 'bg-info bg-opacity-25 text-white border-info border-opacity-50',
                    'processing' => 'bg-warning bg-opacity-25 text-white border-warning border-opacity-50',
                    'shipped' => 'bg-primary bg-opacity-25 text-white border-primary border-opacity-50',
                    'delivered' => 'bg-success bg-opacity-25 text-white border-success border-opacity-50',
                    'cancelled' => 'bg-danger bg-opacity-25 text-white border-danger border-opacity-50',
                    'return_requested' => 'bg-danger bg-opacity-25 text-white border-danger border-opacity-50',
                    'returned' => 'bg-secondary bg-opacity-25 text-white border-white border-opacity-25',
                    default => 'bg-white bg-opacity-10 text-white border-white border-opacity-10',
                };
            @endphp
            <span class="badge {{ $badgeClasses }} border px-3 py-2 rounded-pill fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.8px; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                {{ str_replace('_', ' ', $order->status) }}
            </span>
        </td>
        <td class="py-4 text-center">
            <div class="fw-bold text-info fs-4" style="letter-spacing: -0.5px;">₹{{ number_format($order->total, 2) }}</div>
            <div class="text-white-50 x-small text-uppercase fw-semibold tracking-widest" style="font-size: 0.6rem; opacity: 0.6;">Total Order</div>
        </td>
        <td class="text-end pe-4 py-4">
            <div class="d-flex justify-content-end gap-2 align-items-center">
                {{-- Quick Action Buttons --}}
                @if($order->status == 'placed' || $order->status == 'pending')
                    <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="d-inline status-update-form">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="processing">
                        <button class="btn btn-info btn-sm px-3 rounded-pill fw-bold shadow-sm hover-lift" title="Accept and start processing">
                            <i class="bi bi-check2-circle me-1"></i> Accept
                        </button>
                    </form>
                @elseif($order->status == 'processing')
                    <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="d-inline status-update-form">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="shipped">
                        <button class="btn btn-warning btn-sm px-3 rounded-pill fw-bold shadow-sm hover-lift text-dark" title="Mark as Shipped">
                            <i class="bi bi-truck me-1"></i> Ship Now
                        </button>
                    </form>
                @elseif($order->status == 'shipped')
                    <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="d-inline status-update-form">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="delivered">
                        <button class="btn btn-success btn-sm px-3 rounded-pill fw-bold shadow-sm hover-lift" title="Confirm Delivery">
                            <i class="bi bi-house-check me-1"></i> Deliver
                        </button>
                    </form>
                @elseif($order->status == 'return_requested')
                    <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="d-inline status-update-form">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="returned">
                        <button class="btn btn-danger btn-sm px-3 rounded-pill fw-bold shadow-sm hover-lift" title="Approve Return Request">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Approve Return
                        </button>
                    </form>
                @endif

                {{-- Detailed Actions Dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-outline-white btn-sm px-2 rounded-circle border-white border-opacity-25 hover-bg-glass shadow-none" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical text-white-50"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-2xl border-white border-opacity-10 py-2" style="background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px);">
                        <li><h6 class="dropdown-header text-white-50 x-small text-uppercase tracking-widest pb-2">Modify Status</h6></li>
                        @foreach(['placed','processing','shipped','delivered','cancelled','returned'] as $s)
                            @if($order->status != $s)
                            <li>
                                <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="status-update-form m-0">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $s }}">
                                    <button class="dropdown-item py-2 d-flex align-items-center gap-2">
                                        <i class="bi bi-dot fs-4"></i> {{ ucfirst(str_replace('_', ' ', $s)) }}
                                    </button>
                                </form>
                            </li>
                            @endif
                        @endforeach
                        <li><hr class="dropdown-divider border-white border-opacity-10"></li>
                        <li><a class="dropdown-item py-2 d-flex align-items-center gap-2 text-info" href="{{ route('admin.orders.show', $order->id) }}"><i class="bi bi-eye"></i> View Lifecycle</a></li>
                        <li><a class="dropdown-item py-2 d-flex align-items-center gap-2 text-success" href="{{ route('admin.orders.download', $order->id) }}"><i class="bi bi-file-earmark-pdf"></i> Download Invoice</a></li>
                    </ul>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="border-0">
            {{-- Empty state handled in parent --}}
        </td>
    </tr>
@endforelse
