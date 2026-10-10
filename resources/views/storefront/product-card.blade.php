@php
    $image = $product->images()[0] ?? null;
    $imageUrl = $image
        ? $product->imageUrl($image)
        : asset('images/product-placeholder.svg');

    $isSale = $product->compare_at_price
        && $product->compare_at_price > $product->price;

    $discount = $isSale
        ? round(
            (($product->compare_at_price - $product->price) / $product->compare_at_price) * 100
        )
        : 0;
@endphp

<article class="card h-100 w-100 border shadow-sm bg-white rounded-0 d-flex flex-column">
    <div class="ratio ratio-3x4 bg-light position-relative overflow-hidden">
        <a
            class="position-absolute top-0 start-0 w-100 h-100 z-1"
            href="{{ route('shop.product', $product) }}"
            aria-label="View {{ $product->name }}"
        >
            <img
                class="w-100 h-100 object-fit-contain bg-white"
                src="{{ $imageUrl }}"
                alt="{{ $product->name }}"
                loading="lazy"
                onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
            >
        </a>

        <div class="position-absolute top-0 start-0 w-100 h-100 z-2 pe-none">
            @if ($isSale)
                <span class="position-absolute top-0 start-0 m-2 badge text-bg-light text-danger">
                    -{{ $discount }}%
                </span>
            @elseif ($product->featured)
                <span class="position-absolute top-0 start-0 m-2 badge text-bg-light">
                    Featured
                </span>
            @endif

            <button
                class="btn btn-light position-absolute top-0 end-0 m-2 rounded-circle shadow-sm quick-view-button pe-auto"
                type="button"
                data-bs-toggle="modal"
                data-bs-target="#productQuickView"
                data-product-name="{{ $product->name }}"
                data-product-image="{{ $imageUrl }}"
                data-product-category="{{ $product->categoryRelation?->name ?: ($product->category ?: 'Unstitched') }}"
                data-product-price="{{ $product->price ? number_format((float) $product->price, 0) . ' ' . $product->currency : '' }}"
                data-product-compare-price="{{ $isSale ? number_format((float) $product->compare_at_price, 0) . ' ' . $product->currency : '' }}"
                data-product-description="{{ $product->description }}"
                data-product-in-stock="{{ $product->stock_quantity > 0 ? 'true' : 'false' }}"
                data-product-url="{{ route('shop.product', $product) }}"
                data-cart-url="{{ route('cart.add', $product) }}"
                aria-label="Quick view {{ $product->name }}"
                title="Quick view"
            >
                <i class="fa-solid fa-eye" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="card-body d-flex flex-column p-3">
        <div
            class="small text-uppercase text-secondary text-truncate"
            title="{{ $product->categoryRelation?->name ?: ($product->category ?: 'Unstitched') }}"
        >
            {{ $product->categoryRelation?->name ?: ($product->category ?: 'Unstitched') }}
        </div>

        <a
            class="text-decoration-none text-dark"
            href="{{ route('shop.product', $product) }}"
            title="{{ $product->name }}"
        >
            <h3 class="h6 fw-semibold mt-1 mb-0 text-truncate">
                {{ $product->name }}
            </h3>
        </a>

        <div class="mt-auto pt-3">
            @if ($product->price)
                <div class="fw-bold">
                    {{ number_format((float) $product->price, 0) }} {{ $product->currency }}

                    @if ($isSale)
                        <span class="text-secondary text-decoration-line-through small ms-1 fw-normal">
                            {{ number_format((float) $product->compare_at_price, 0) }}
                            {{ $product->currency }}
                        </span>
                    @endif
                </div>
            @endif

            <div class="pt-3">
                @if ($product->stock_quantity > 0)
                    <form method="POST" action="{{ route('cart.add', $product) }}">
                        @csrf

                        <input
                            type="hidden"
                            name="quantity"
                            value="1"
                        >

                        <button
                            class="btn btn-dark w-100 text-uppercase fw-bold"
                            type="submit"
                        >
                            <i class="fa-solid fa-cart-shopping me-2" aria-hidden="true"></i>Add to cart
                        </button>
                    </form>
                @else
                    <button
                        class="btn btn-outline-secondary w-100"
                        type="button"
                        disabled
                    >
                        Out of stock
                    </button>
                @endif
            </div>
        </div>
    </div>
</article>
