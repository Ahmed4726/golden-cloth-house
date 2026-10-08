@php
    $image = $product->images()[0] ?? null;
    $imageUrl = $image
        ? (str_starts_with($image, 'http') ? $image : $product->imageUrl($image))
        : null;
    $imageAvailable = $image ? $product->imageExists($image) : false;
    $isSale = $product->compare_at_price && $product->compare_at_price > $product->price;
    $discount = $isSale
        ? round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100)
        : 0;
@endphp

<article class="card h-100 w-100 border shadow-sm bg-white rounded-0 d-flex flex-column">
    <a class="text-decoration-none text-dark" href="{{ route('shop.product', $product) }}" aria-label="View {{ $product->name }}">
        <div class="ratio ratio-3x4 bg-light position-relative overflow-hidden">
            @if ($isSale)
                <span class="position-absolute top-0 start-0 m-2 badge text-bg-light text-danger z-1">-{{ $discount }}%</span>
            @elseif ($product->featured)
                <span class="position-absolute top-0 start-0 m-2 badge text-bg-light z-1">Featured</span>
            @endif

            @if ($imageUrl && $imageAvailable)
                <img
                    class="w-100 h-100 object-fit-contain bg-white"
                    src="{{ $imageUrl }}"
                    alt="{{ $product->name }}"
                    loading="lazy"
                >
            @else
                <div class="d-flex flex-column align-items-center justify-content-center gap-2 text-secondary p-3 text-center">
                    <i class="bi bi-image fs-2"></i>
                    <span class="small">Image unavailable</span>
                </div>
            @endif
        </div>
    </a>

    <div class="card-body d-flex flex-column p-3">
        <div class="small text-uppercase text-secondary text-truncate" title="{{ $product->categoryRelation?->name ?: ($product->category ?: 'Unstitched') }}">
            {{ $product->categoryRelation?->name ?: ($product->category ?: 'Unstitched') }}
        </div>

        <a class="text-decoration-none text-dark" href="{{ route('shop.product', $product) }}" title="{{ $product->name }}">
            <h3 class="h6 fw-semibold mt-1 mb-0 text-truncate">{{ $product->name }}</h3>
        </a>

        <div class="mt-auto pt-3">
            @if ($product->price)
                <div class="fw-bold">
                    {{ number_format((float) $product->price, 0) }} {{ $product->currency }}
                    @if ($isSale)
                        <span class="text-secondary text-decoration-line-through small ms-1 fw-normal">
                            {{ number_format((float) $product->compare_at_price, 0) }} {{ $product->currency }}
                        </span>
                    @endif
                </div>
            @endif

            <div class="pt-3">
                @if ($product->stock_quantity > 0)
                    <form method="POST" action="{{ route('cart.add', $product) }}">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button class="btn btn-dark w-100 text-uppercase fw-bold" type="submit">Add to bag</button>
                    </form>
                @else
                    <button class="btn btn-outline-secondary w-100" type="button" disabled>Out of stock</button>
                @endif
            </div>
        </div>
    </div>
</article>
