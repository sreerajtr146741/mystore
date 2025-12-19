@foreach($products as $p)
    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 product-item">
        @include('partials.product-card', ['p' => $p])
    </div>
@endforeach
