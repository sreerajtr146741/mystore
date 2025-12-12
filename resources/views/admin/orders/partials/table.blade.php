@if($orders->isEmpty())
    <div class="card p-3 text-center text-muted py-5">
        <i class="bi bi-box-seam display-4 d-block mb-3"></i>
        No orders in this stage.
    </div>
@else
    <div class="card p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>
                            {{ $order->user->name ?? 'Guest' }}<br>
                            <small class="text-muted">{{ $order->user->email ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $order->items->count() }} items</td>
                        <td>₹{{ number_format($order->total, 2) }}</td>
                        <td>{{ $order->created_at->diffForHumans() }}</td>
                        <td class="text-end">
                            <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $nextStatus }}">
                                <button type="submit" class="btn btn-sm {{ $btnClass }}">
                                    {{ $btnLabel }} <i class="bi bi-arrow-right list-inline-item ms-1"></i>
                                </button>
                            </form>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
