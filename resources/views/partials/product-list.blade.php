@foreach($products as $p)
    <div class="col-6 col-md-4 col-lg-3 product-item">
        @include('partials.product-card', ['p' => $p])
    </div>
@endforeach
