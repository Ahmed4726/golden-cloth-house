@php
    $galleryImageUrls = array_map(
        fn ($image) => $product->imageUrl($image),
        $product->images()
    );
    $galleryImageUrls = $galleryImageUrls ?: [asset('images/product-placeholder.svg')];
    $imageUrl = $galleryImageUrls[0];

    $isSale = $product->compare_at_price
        && $product->compare_at_price > $product->price;

    $discount = $isSale
        ? round(
            (($product->compare_at_price - $product->price) / $product->compare_at_price) * 100
        )
        : 0;
    $includedPieces = $product->included_pieces ?? [];
    $pieceLabels = [
        'kameez' => 'Kameez (shirt)',
        'dupatta' => 'Dupatta',
        'trouser' => 'Trouser / Shalwar',
    ];
    $pieceLengths = $product->piece_lengths ?? [];
@endphp

<article class="card h-100 w-100 border-0 shadow-sm bg-white rounded-3 overflow-hidden d-flex flex-column">
    <div>
        <div
            id="productCardGallery{{ $product->id }}"
            class="carousel slide product-card-gallery ratio ratio-4x3 w-100 bg-light position-relative overflow-hidden"
            data-bs-interval="false"
            data-bs-touch="true"
        >
            <div class="carousel-inner h-100">
                @foreach ($galleryImageUrls as $index => $galleryImageUrl)
                    <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                        <a
                            class="d-block w-100 h-100"
                            href="{{ route('shop.product', $product->slug) }}"
                            aria-label="View {{ $product->name }}"
                        >
                            <img
                                class="d-block w-100 h-100 object-fit-contain bg-white"
                                src="{{ $galleryImageUrl }}"
                                alt="{{ $product->name }} image {{ $index + 1 }}"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
                            >
                        </a>
                    </div>
                @endforeach
            </div>

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
                    class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 rounded-circle shadow-sm pe-auto"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#productQuickView"
                    data-product-name="{{ $product->name }}"
                    data-product-image="{{ $imageUrl }}"
                    data-product-images="{{ json_encode($galleryImageUrls) }}"
                    data-product-category="{{ $product->categoryRelation?->name ?: ($product->category ?: 'Unstitched') }}"
                    data-product-price="{{ $product->price ? number_format((float) $product->price, 0) . ' ' . $product->currency : '' }}"
                    data-product-amount="{{ (float) $product->price }}"
                    data-product-currency="{{ $product->currency }}"
                    data-product-compare-price="{{ $isSale ? number_format((float) $product->compare_at_price, 0) . ' ' . $product->currency : '' }}"
                    data-product-description="{{ $product->description }}"
                    data-product-in-stock="{{ $product->stock_quantity > 0 ? 'true' : 'false' }}"
                    data-product-stock="{{ $product->stock_quantity }}"
                    data-product-piece-count="{{ $product->piece_count ?? count($includedPieces) }}"
                    data-product-included-pieces="{{ implode(',', $includedPieces) }}"
                    data-product-piece-length-kameez="{{ $product->piece_lengths['kameez'] ?? '' }}"
                    data-product-piece-length-dupatta="{{ $product->piece_lengths['dupatta'] ?? '' }}"
                    data-product-piece-length-trouser="{{ $product->piece_lengths['trouser'] ?? '' }}"
                    data-product-url="{{ route('shop.product', $product->slug) }}"
                    data-cart-url="{{ route('cart.add', $product) }}"
                    aria-label="Quick view {{ $product->name }}"
                    title="Quick view"
                >
                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        @if (count($galleryImageUrls) > 1)
            <div class="carousel-indicators position-static justify-content-center gap-0 w-100 m-0 mt-2">
                @foreach ($galleryImageUrls as $index => $galleryImageUrl)
                    <button
                        type="button"
                        class="bg-dark {{ $index === 0 ? 'active' : '' }}"
                        data-bs-target="#productCardGallery{{ $product->id }}"
                        data-bs-slide-to="{{ $index }}"
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-label="Show image {{ $index + 1 }} of {{ $product->name }}"
                    ></button>
                @endforeach
            </div>
        @endif
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
            href="{{ route('shop.product', $product->slug) }}"
            title="{{ $product->name }}"
        >
            <h3 class="h6 fw-semibold mt-1 mb-0 text-truncate">
                {{ $product->name }}
            </h3>
        </a>

        <div class="pt-3">
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
        </div>

        <div class="mt-auto pt-3">
            @if ($product->stock_quantity > 0)
                <div class="row g-2">
                    <form class="{{ $whatsappNumber ? 'col-9 col-xxl-8' : 'col' }}" method="POST" action="{{ route('cart.add', $product) }}">
                        @csrf

                        <input type="hidden" name="quantity" value="1">

                        <button
                            class="btn btn-dark w-100 h-100 py-2 text-uppercase fw-bold d-inline-flex align-items-center justify-content-center gap-2"
                            type="submit"
                            aria-label="Add {{ $product->name }} to cart"
                        >
                            <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
                            <span class="visually-hidden">Add to cart</span>
                        </button>
                    </form>

                    @if ($whatsappNumber)
                        @php
                            $whatsappOrderNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);
                            $whatsappSubtotal = (float) $product->price;
                            $whatsappShipping = $whatsappSubtotal > 0 && $whatsappSubtotal < config('store.free_shipping_threshold')
                                ? (float) config('store.shipping_fee')
                                : 0;
                            $whatsappOrderPieces = array_map(
                                fn ($piece) => ($pieceLabels[$piece] ?? $piece)
                                    .(empty($pieceLengths[$piece]) ? '' : ' ('.$pieceLengths[$piece].' in)'),
                                $includedPieces
                            );
                            $whatsappOrderMessage = "Hello, I would like to order this product:\n"
                                .$product->name
                                ."\nQuantity: 1"
                                ."\nUnit price: ".number_format((float) $product->price, 0).' '.$product->currency
                                ."\nSubtotal: ".number_format($whatsappSubtotal, 0).' '.$product->currency
                                ."\nDelivery: ".($whatsappShipping > 0
                                    ? number_format($whatsappShipping, 0).' '.$product->currency
                                    : 'Free')
                                ."\nTotal: ".number_format($whatsappSubtotal + $whatsappShipping, 0).' '.$product->currency
                                .($product->piece_count
                                    ? "\nTotal pieces: ".$product->piece_count
                                    : '')
                                .($whatsappOrderPieces
                                    ? "\nIncluded pieces: ".implode(', ', $whatsappOrderPieces)
                                    : '')
                                ."\nProduct link: ".route('shop.product', $product->slug);
                        @endphp

                        <a
                            class="col-3 col-xxl-4 btn btn-success rounded-3 py-2 d-inline-flex align-items-center justify-content-center gap-2"
                            href="https://wa.me/{{ $whatsappOrderNumber }}?text={{ rawurlencode($whatsappOrderMessage) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Order {{ $product->name }} via WhatsApp"
                            title="Order via WhatsApp"
                        >
                            <i class="fa-brands fa-whatsapp fs-5" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
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
</article>
