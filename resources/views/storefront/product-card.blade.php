@php
    $image = $product->images()[0] ?? null;
@endphp

<a class="product-card" href="{{ route('shop.product', $product) }}">
    <div class="card-image">
        @if ($product->featured)
            <span class="badge">Featured</span>
        @endif

        @if ($image)
            <img
                src="{{ str_starts_with($image, 'http') ? $image : asset('storage/'.$image) }}"
                alt="{{ $product->name }}"
                loading="lazy"
            >
        @else
            <div class="placeholder">{{ config('store.name') }}</div>
        @endif
    </div>

    <div class="card-body">
        <div class="muted">{{ $product->category ?: config('store.name') }}</div>
        <h3>{{ $product->name }}</h3>

        @if ($product->price)
            <span class="price">{{ number_format((float) $product->price, 0) }} {{ $product->currency }}</span>
        @endif

        @if ($product->compare_at_price && $product->compare_at_price > $product->price)
            <span class="compare">{{ number_format((float) $product->compare_at_price, 0) }} {{ $product->currency }}</span>
        @endif
    </div>
</a>
