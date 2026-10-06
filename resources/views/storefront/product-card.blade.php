@php
    $image = $product->images()[0] ?? null;
    $isSale = $product->compare_at_price && $product->compare_at_price > $product->price;
    $discount = $isSale
        ? round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100)
        : 0;
@endphp

<article class="product-card">
    <a href="{{ route('shop.product', $product) }}" aria-label="View {{ $product->name }}">
        <div class="card-image">
            @if ($isSale)
                <span class="product-badge sale">-{{ $discount }}%</span>
            @elseif ($product->featured)
                <span class="product-badge">Featured</span>
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
    </a>

    <div class="card-body">
        <div class="card-category">{{ $product->categoryRelation?->name ?: ($product->category ?: 'Unstitched') }}</div>
        <a href="{{ route('shop.product', $product) }}">
            <h3>{{ $product->name }}</h3>
        </a>

        @if ($product->price)
            <span class="price">{{ number_format((float) $product->price, 0) }} {{ $product->currency }}</span>
        @endif

        @if ($isSale)
            <span class="compare">{{ number_format((float) $product->compare_at_price, 0) }} {{ $product->currency }}</span>
        @endif

        @if ($product->stock_quantity > 0)
            <div class="card-actions">
                <form method="POST" action="{{ route('cart.add', $product) }}">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn" type="submit">Add to bag</button>
                </form>
            </div>
        @else
            <div class="card-actions">
                <span class="muted">Out of stock</span>
            </div>
        @endif
    </div>
</article>
