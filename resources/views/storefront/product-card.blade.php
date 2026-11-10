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

<article class="card h-100 w-100 border shadow-sm bg-white rounded-0 d-flex flex-column">
    <div class="product-card-media">
        <div
            id="productCardGallery{{ $product->id }}"
            class="carousel slide product-card-gallery ratio ratio-7x5 bg-light position-relative overflow-hidden"
            data-bs-interval="false"
            data-bs-touch="true"
        >
            <div class="carousel-inner h-100">
                @foreach ($galleryImageUrls as $index => $galleryImageUrl)
                    <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                        <a
                            class="d-block w-100 h-100"
                            href="{{ route('shop.product', $product) }}"
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
                    class="btn btn-light position-absolute top-0 end-0 m-2 rounded-circle shadow-sm quick-view-button pe-auto"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#productQuickView"
                    data-product-name="{{ $product->name }}"
                    data-product-image="{{ $imageUrl }}"
                    data-product-images="{{ json_encode($galleryImageUrls) }}"
                    data-product-category="{{ $product->categoryRelation?->name ?: ($product->category ?: 'Unstitched') }}"
                    data-product-price="{{ $product->price ? number_format((float) $product->price, 0) . ' ' . $product->currency : '' }}"
                    data-product-compare-price="{{ $isSale ? number_format((float) $product->compare_at_price, 0) . ' ' . $product->currency : '' }}"
                    data-product-description="{{ $product->description }}"
                    data-product-in-stock="{{ $product->stock_quantity > 0 ? 'true' : 'false' }}"
                    data-product-stock="{{ $product->stock_quantity }}"
                    data-product-piece-count="{{ $product->piece_count ?? count($includedPieces) }}"
                    data-product-included-pieces="{{ implode(',', $includedPieces) }}"
                    data-product-piece-length-kameez="{{ $product->piece_lengths['kameez'] ?? '' }}"
                    data-product-piece-length-dupatta="{{ $product->piece_lengths['dupatta'] ?? '' }}"
                    data-product-piece-length-trouser="{{ $product->piece_lengths['trouser'] ?? '' }}"
                    data-product-url="{{ route('shop.product', $product) }}"
                    data-cart-url="{{ route('cart.add', $product) }}"
                    aria-label="Quick view {{ $product->name }}"
                    title="Quick view"
                >
                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        @if (count($galleryImageUrls) > 1)
            <div class="carousel-indicators product-card-indicators">
                @foreach ($galleryImageUrls as $index => $galleryImageUrl)
                    <button
                        type="button"
                        data-bs-target="#productCardGallery{{ $product->id }}"
                        data-bs-slide-to="{{ $index }}"
                        class="{{ $index === 0 ? 'active' : '' }}"
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
                    <div class="d-flex gap-2">
                        <form class="flex-grow-1 product-card-cart-form" method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf

                            <input type="hidden" name="quantity" value="1">

                            <button
                                class="btn btn-dark w-100 text-uppercase fw-bold store-add-to-cart product-card-cart-button"
                                type="submit"
                            >
                                <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
                                <span class="visually-hidden">Add to cart</span>
                                <span class="d-none d-lg-inline">Add</span>
                                <span class="d-none d-xl-inline">to cart</span>
                            </button>
                        </form>

                        @if ($whatsappNumber)
                            @php
                                $whatsappOrderNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);
                                $whatsappOrderPieces = array_map(
                                    fn ($piece) => ($pieceLabels[$piece] ?? $piece)
                                        .(empty($pieceLengths[$piece]) ? '' : ' ('.$pieceLengths[$piece].' in)'),
                                    $includedPieces
                                );
                                $whatsappOrderMessage = "Hello, I would like to order this product:\n"
                                    .$product->name
                                    ."\nQuantity: 1"
                                    ."\nUnit price: ".number_format((float) $product->price, 0).' '.$product->currency
                                    .($product->piece_count
                                        ? "\nTotal pieces: ".$product->piece_count
                                        : '')
                                    .($whatsappOrderPieces
                                        ? "\nIncluded pieces: ".implode(', ', $whatsappOrderPieces)
                                        : '')
                                    ."\nProduct link: ".route('shop.product', $product);
                            @endphp

                            <a
                                class="btn btn-success product-card-whatsapp-button"
                                href="https://wa.me/{{ $whatsappOrderNumber }}?text={{ rawurlencode($whatsappOrderMessage) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Order {{ $product->name }} via WhatsApp"
                                title="Order via WhatsApp"
                            >
                                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                @else
                    <button
                        class="btn btn-outline-secondary w-100 store-add-to-cart"
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
