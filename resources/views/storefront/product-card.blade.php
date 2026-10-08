@php
    $image = $product->images()[0] ?? null;
    $isSale = $product->compare_at_price && $product->compare_at_price > $product->price;
    $discount = $isSale
        ? round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100)
        : 0;
@endphp

<article class="card h-100 border-0 bg-white">
    <a class="text-decoration-none text-dark" href="{{ route('shop.product', $product) }}" aria-label="View {{ $product->name }}">
        <div class="ratio ratio-3x4 bg-light position-relative overflow-hidden">
            @if ($isSale)
                <span class="position-absolute top-0 start-0 m-2 badge text-bg-light text-danger z-1">-{{ $discount }}%</span>
            @elseif ($product->featured)
                <span class="position-absolute top-0 start-0 m-2 badge text-bg-light z-1">Featured</span>
            @endif

            @if ($image)
                <img class="w-100 h-100 object-fit-contain bg-white" src="{{ str_starts_with($image, 'http') ? $image : asset('storage/'.$image) }}" alt="{{ $product->name }}" loading="lazy">
            @else
                <div class="d-flex align-items-center justify-content-center text-secondary">{{ config('store.name') }}</div>
            @endif
        </div>
    </a>

    <div class="card-body px-1 pt-3">
        <div class="small text-uppercase text-secondary">{{ $product->categoryRelation?->name ?: ($product->category ?: 'Unstitched') }}</div>
        <a class="text-decoration-none text-dark" href="{{ route('shop.product', $product) }}">
            <h3 class="h6 fw-semibold mt-1 mb-2">{{ $product->name }}</h3>
        </a>

        @if ($product->price)
            <span class="fw-bold">{{ number_format((float) $product->price, 0) }} {{ $product->currency }}</span>
        @endif

        @if ($isSale)
            <span class="text-secondary text-decoration-line-through small ms-1">{{ number_format((float) $product->compare_at_price, 0) }} {{ $product->currency }}</span>
        @endif

        <div class="mt-3">
            @if ($product->stock_quantity > 0)
                <form method="POST" action="{{ route('cart.add', $product) }}">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn-dark w-100 text-uppercase fw-bold" type="submit">Add to bag</button>
                </form>
            @else
                <span class="text-secondary">Out of stock</span>
            @endif
        </div>
    </div>
</article>
