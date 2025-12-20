@foreach($orders as $order)
    <div class="card border-0 shadow-lg mb-4 rounded-4 overflow-hidden hover:shadow-2xl transition-all duration-300">
        <div class="card-header bg-white border-bottom-0 p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-indigo-100 text-indigo-700 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-box-open fs-5"></i>
                </div>
                <div>
                    <div class="text-xs text-uppercase text-muted fw-bold tracking-wider">Order ID</div>
                    <div class="fw-bold fs-5 text-dark">#{{ $order->id }}</div>
                </div>
            </div>
            <div>
               <div class="text-end">
                    @if($order->status == 'delivered')
                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold border border-success-subtle">
                            <i class="fas fa-check-circle me-1"></i> Delivered
                        </span>
                    @elseif($order->status == 'shipped')
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold border border-primary-subtle">
                            <i class="fas fa-truck me-1"></i> Shipped
                        </span>
                    @elseif($order->status == 'processing')
                        <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill fw-bold border border-info-subtle">
                            <i class="fas fa-cog fa-spin me-1"></i> Processing
                        </span>
                    @elseif($order->status == 'out_for_delivery')
                        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fw-bold border border-warning-subtle">
                            <i class="fas fa-truck-moving me-1"></i> Out for Delivery
                        </span>
                    @elseif($order->status == 'return_requested')
                        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fw-bold border border-warning-subtle">
                            <i class="fas fa-undo me-1"></i> Return Requested
                        </span>
                    @elseif($order->status == 'returned')
                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill fw-bold border border-secondary-subtle">
                            <i class="fas fa-check-double me-1"></i> Returned
                        </span>
                    @else
                        <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-bold border">
                            <i class="fas fa-clock me-1"></i> {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    @endif
               </div>
               <div class="text-xs text-muted text-end mt-1">{{ $order->created_at->format('D, d M Y • h:i A') }}</div>
            </div>
        </div>
        
        <div class="card-body p-4 bg-light bg-opacity-25">
            <div class="row align-items-center g-4">
                <!-- Product Previews -->
                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-3 overflow-auto pb-2" style="scrollbar-width: thin;">
                        @foreach($order->items->take(4) as $item)
                            <div class="position-relative" style="min-width: 70px;">
                                <div class="ratio ratio-1x1 rounded-3 overflow-hidden border bg-white">
                                    <img src="{{ \Illuminate\Support\Str::startsWith($item->product->image, 'http') ? $item->product->image : asset('storage/'.$item->product->image) }}" 
                                         class="object-fit-cover" alt="Product">
                                </div>
                                @if($loop->iteration == 4 && $order->items->count() > 4)
                                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex align-items-center justify-content-center text-white fw-bold rounded-3">
                                        +{{ $order->items->count() - 3 }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        @if($order->items->count() == 1)
                            <div class="nav flex-column">
                                <div class="fw-semibold text-dark">{{ $order->items->first()->product->name }}</div>
                                <div class="small text-muted">Qnt: {{ $order->items->first()->qty }}</div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Meta & Actions -->
                <div class="col-md-5">
                    <div class="d-flex justify-content-between align-items-center h-100">
                        <div>
                            <div class="small text-muted text-uppercase fw-bold">Total Amount</div>
                            <div class="fs-4 fw-bold text-dark">₹{{ number_format($order->total, 2) }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            @if(in_array($order->status, ['placed', 'processing']))
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 fw-bold shadow-sm hover-scale" title="Cancel Order">
                                        <i class="fas fa-times-circle"></i> Cancel
                                    </button>
                                </form>
                            @endif

                            @php
                                $daysSinceDelivery = $order->updated_at->diffInDays(now());
                                $canReturn = $daysSinceDelivery <= 7;
                            @endphp

                            @if($order->status == 'delivered')
                                @if($canReturn)
                                    <form action="{{ route('orders.return', $order->id) }}" method="POST" onsubmit="return confirm('Request a return for this order?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-2 fw-bold shadow-sm hover-scale" title="Return Order ({{ 7 - $daysSinceDelivery }} days remaining)">
                                            <i class="fas fa-undo"></i> Return
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('orders.download', $order->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-bold shadow-sm hover-scale" title="Download Invoice">
                                    <i class="bi bi-download"></i>
                                </a>
                            @endif
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-bold shadow-sm hover-scale">
                                View <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
