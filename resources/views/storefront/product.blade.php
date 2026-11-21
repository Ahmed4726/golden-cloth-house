@extends('storefront.layout')

@php
    $images = $product->images();
    $imageUrls = array_map(
        fn ($image) => $product->imageUrl($image, 1280),
        $images
    );
    $firstImage = $images[0] ?? null;
    $suitPieceLabels = [
        'kameez' => 'Kameez (shirt)',
        'dupatta' => 'Dupatta',
        'trouser' => 'Trouser / Shalwar',
    ];
    $includedPieces = $product->included_pieces ?? [];
    $pieceLengths = $product->piece_lengths ?? [];
    $shareImage = $firstImage
        ? url($product->imageUrl($firstImage))
        : asset('images/product-placeholder.svg');

    $title = $product->name . ' | ' . config('store.name');

    $description = \Illuminate\Support\Str::limit(
        strip_tags(
            $product->description
                ?: 'Shop ' . $product->name . ' from ' . config('store.name') . '. Cash on Delivery available across Pakistan.'
        ),
        155
    );

    $schema = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => strip_tags($product->description ?: ''),
        'sku' => $product->sku,
        'category' => $product->category,
        'image' => array_map(
            fn ($image) => url($product->imageUrl($image)),
            $images
        ),
        'offers' => [
            '@type' => 'Offer',
            'priceCurrency' => $product->currency,
            'price' => (float) $product->price,
            'availability' => $product->stock_quantity > 0
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'url' => route('shop.product', $product->slug),
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $ogType = 'product';
@endphp

@section('content')
    <div class="container py-4">
        <nav class="small text-secondary mb-3" aria-label="Breadcrumb">
            <a
                class="text-dark text-decoration-none"
                href="{{ route('home') }}"
            >
                Home
            </a>

            <span class="mx-1">/</span>

            <a
                class="text-dark text-decoration-none"
                href="{{ route('shop') }}"
            >
                Shop
            </a>

            <span class="mx-1">/</span>
            <span>{{ $product->name }}</span>
        </nav>

        <header class="mb-4">
            <div class="small text-uppercase text-secondary fw-bold">
                {{ $product->categoryRelation?->name ?: ($product->category ?: 'UNSTITCHED') }}
            </div>
            <h1 class="display-6 fw-normal mt-1 mb-0">{{ $product->name }}</h1>
        </header>

        <section class="row g-4 g-lg-5">
            <div class="col-lg-7">
                <div>
                    <div
                        id="productGallery"
                        class="carousel slide product-detail-gallery ratio ratio-4x3 bg-light rounded-3 overflow-hidden"
                        data-bs-interval="false"
                        data-bs-touch="true"
                        aria-label="Product images"
                        data-image-viewer-gallery="{{ json_encode($imageUrls, JSON_UNESCAPED_SLASHES) }}"
                        data-image-viewer-title="{{ $product->name }}"
                    >
                        <div class="carousel-inner h-100">
                            @forelse ($images as $index => $image)
                                @php
                                    $imageUrl = $product->imageUrl($image, 960);
                                @endphp

                                <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                                    <button
                                        class="product-image-viewer-trigger position-relative d-flex w-100 h-100 align-items-center justify-content-center border-0 bg-white p-0"
                                        type="button"
                                        data-image-viewer-index="{{ $index }}"
                                        aria-label="Enlarge {{ $product->name }} image {{ $index + 1 }}"
                                    >
                                        <img
                                            class="d-block w-100 h-100 object-fit-contain bg-white product-image-zoom"
                                            src="{{ $imageUrl }}"
                                            srcset="{{ $product->imageUrl($image, 480) }} 480w, {{ $product->imageUrl($image, 640) }} 640w, {{ $imageUrl }} 960w, {{ $product->imageUrl($image, 1280) }} 1280w"
                                            sizes="(min-width: 992px) 58vw, 100vw"
                                            alt="{{ $product->name }} image {{ $index + 1 }}"
                                            loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                            onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
                                        >
                                        <span class="position-absolute bottom-0 end-0 m-3 d-flex align-items-center justify-content-center rounded-circle bg-white p-2 shadow" aria-hidden="true">
                                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        </span>
                                    </button>
                                </div>
                            @empty
                                <div class="carousel-item h-100 active">
                                    <img
                                        class="d-block w-100 h-100 object-fit-contain bg-white product-image-zoom"
                                        src="{{ asset('images/product-placeholder.svg') }}"
                                        alt="{{ $product->name }}"
                                    >
                                </div>
                            @endforelse
                        </div>

                        @if (count($images) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#productGallery" data-bs-slide="prev" aria-label="Previous product image">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productGallery" data-bs-slide="next" aria-label="Next product image">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </button>
                        @endif
                    </div>

                    @if (count($images) > 1)
                        <div class="d-flex flex-nowrap gap-3 mt-4 overflow-auto" aria-label="Choose a product image">
                            @foreach ($images as $index => $image)
                                <button
                                    type="button"
                                    class="col-2 col-md-1 btn btn-outline-secondary p-2 {{ $index === 0 ? 'active' : '' }}"
                                    data-bs-target="#productGallery"
                                    data-bs-slide-to="{{ $index }}"
                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                    aria-label="View product image {{ $index + 1 }}"
                                >
                                    <div class="ratio ratio-1x1">
                                        <img
                                            class="w-100 h-100 object-fit-contain"
                                            src="{{ $product->imageUrl($image, 320) }}"
                                            alt="{{ $product->name }} image {{ $index + 1 }}"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <div class="carousel-indicators position-static justify-content-center gap-0 w-100 m-0 mt-2">
                            @foreach ($images as $index => $image)
                                <button
                                    type="button"
                                    class="bg-dark {{ $index === 0 ? 'active' : '' }}"
                                    data-bs-target="#productGallery"
                                    data-bs-slide-to="{{ $index }}"
                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                    aria-label="Show product image {{ $index + 1 }}"
                                ></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-5">
                <div>
                    @if ($product->price)
                        <div class="fs-4 fw-bold mb-2">
                            {{ number_format((float) $product->price, 0) }}
                            {{ $product->currency }}

                            @if (
                                $product->compare_at_price
                                && $product->compare_at_price > $product->price
                            )
                                <span class="fs-6 text-secondary text-decoration-line-through ms-2">
                                    {{ number_format((float) $product->compare_at_price, 0) }}
                                    {{ $product->currency }}
                                </span>
                            @endif
                        </div>
                    @endif

                    @if ($product->stock_quantity > 0)
                        <div class="small text-success text-uppercase fw-bold">
                            In stock · Ready to ship
                        </div>
                    @else
                        <div class="small text-danger text-uppercase fw-bold">
                            Sold out
                        </div>
                    @endif

                    <div class="alert alert-light border my-3" role="note">
                        <div class="fw-semibold">Shipping policy</div>
                        <div class="small text-secondary">
                            Free delivery on orders of {{ number_format((float) config('store.free_shipping_threshold'), 0) }} {{ config('store.currency') }} or more.
                            Orders below {{ number_format((float) config('store.free_shipping_threshold'), 0) }} {{ config('store.currency') }} have a flat
                            {{ number_format((float) config('store.shipping_fee'), 0) }} {{ config('store.currency') }} delivery fee.
                        </div>
                    </div>

                    <div class="text-secondary my-4">
                        {!! nl2br(e($product->description)) !!}
                    </div>

                    @if ($product->piece_count || $includedPieces)
                        <div class="border rounded p-3 mb-4">
                            <div class="fw-semibold mb-2">
                                Suit details · {{ $product->piece_count ?? count($includedPieces) }} pieces
                            </div>
                            @if ($includedPieces)
                                <ul class="list-unstyled small mb-0">
                                    @foreach ($includedPieces as $piece)
                                        <li>
                                            {{ $suitPieceLabels[$piece] ?? $piece }}
                                            @if (! empty($pieceLengths[$piece]))
                                                <span class="text-secondary">· {{ $pieceLengths[$piece] }} in</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                    @if ($product->kameezFabric || $product->trouserFabric || $product->dupattaFabric || $product->season)
                        <div class="border rounded p-3 mb-4">
                            <div class="fw-semibold mb-2">Fabric & season</div>
                            <ul class="list-unstyled small mb-0">
                                @if ($product->kameezFabric)<li>Kameez: {{ $product->kameezFabric->name }}</li>@endif
                                @if ($product->trouserFabric)<li>Shalwar / trouser: {{ $product->trouserFabric->name }}</li>@endif
                                @if ($product->dupattaFabric)<li>Dupatta: {{ $product->dupattaFabric->name }}</li>@endif
                                @if ($product->season)<li>Season: {{ $product->season }}</li>@endif
                            </ul>
                        </div>
                    @endif

                    @if ($product->stock_quantity > 0)
                        <form
                            method="POST"
                            action="{{ route('cart.add', $product) }}"
                        >
                            @csrf

                            <div class="row g-2 align-items-end">
                                <div class="col-4 col-sm-3">
                                    <label
                                        class="form-label small fw-bold text-uppercase"
                                        for="quantity"
                                    >
                                        Quantity
                                    </label>

                                    <input
                                        class="form-control"
                                        id="quantity"
                                        type="number"
                                        name="quantity"
                                        value="1"
                                        min="1"
                                        max="{{ min($product->stock_quantity, 20) }}"
                                    >
                                </div>

                                <div class="col">
                                    <button
                                        class="btn btn-dark w-100 text-uppercase fw-bold"
                                        type="submit"
                                    >
                                        <i class="fa-solid fa-cart-shopping me-2" aria-hidden="true"></i>Add to cart
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if ($whatsappNumber)
                            @php
                                $whatsappOrderNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);
                                $whatsappSubtotal = (float) $product->price;
                                $whatsappShipping = $whatsappSubtotal > 0 && $whatsappSubtotal < config('store.free_shipping_threshold')
                                    ? (float) config('store.shipping_fee')
                                    : 0;
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
                                    .($includedPieces
                                        ? "\nIncluded pieces: ".implode(', ', array_map(
                                            fn ($piece) => ($suitPieceLabels[$piece] ?? $piece)
                                                .(empty($pieceLengths[$piece]) ? '' : ' ('.$pieceLengths[$piece].' in)'),
                                            $includedPieces
                                        ))
                                        : '')
                                    ."\nProduct link: ".route('shop.product', $product->slug);
                            @endphp

                            <a
                                class="btn btn-success w-100 py-2 mt-2 text-uppercase fw-bold"
                                href="https://wa.me/{{ $whatsappOrderNumber }}?text={{ rawurlencode($whatsappOrderMessage) }}"
                                data-whatsapp-order
                                data-whatsapp-number="{{ $whatsappOrderNumber }}"
                                data-product-name="{{ $product->name }}"
                                data-product-price="{{ number_format((float) $product->price, 0) }} {{ $product->currency }}"
                                data-product-amount="{{ (float) $product->price }}"
                                data-product-currency="{{ $product->currency }}"
                                data-shipping-fee="{{ config('store.shipping_fee') }}"
                                data-free-shipping-threshold="{{ config('store.free_shipping_threshold') }}"
                                data-product-piece-count="{{ $product->piece_count ?? count($includedPieces) }}"
                                data-product-pieces="{{ implode(', ', array_map(
                                    fn ($piece) => ($suitPieceLabels[$piece] ?? $piece)
                                        .(empty($pieceLengths[$piece]) ? '' : ' ('.$pieceLengths[$piece].' in)'),
                                    $includedPieces
                                )) }}"
                                data-product-url="{{ route('shop.product', $product->slug) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                <i class="fa-brands fa-whatsapp me-2" aria-hidden="true"></i>Order via WhatsApp
                            </a>
                        @endif

                        <div class="row row-cols-3 g-2 mt-3">
                            <div class="col">
                                <div class="bg-light p-3 text-center small h-100">
                                    Cash on Delivery
                                </div>
                            </div>

                            <div class="col">
                                <div class="bg-light p-3 text-center small h-100">
                                    Pakistan-wide delivery
                                </div>
                            </div>

                            <div class="col">
                                <div class="bg-light p-3 text-center small h-100">
                                    Secure checkout
                                </div>
                            </div>
                        </div>
                    @endif

                    <div
                        class="accordion accordion-flush border-top mt-4"
                        id="productInfo"
                    >
                        @if ($product->sku)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button
                                        class="accordion-button collapsed px-0"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#productDetails"
                                    >
                                        Product details
                                    </button>
                                </h2>

                                <div
                                    id="productDetails"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#productInfo"
                                >
                                    <div class="accordion-body px-0 text-secondary">
                                        SKU: {{ $product->sku }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button
                                    class="accordion-button collapsed px-0"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#careInfo"
                                >
                                    Care instructions
                                </button>
                            </h2>

                            <div
                                id="careInfo"
                                class="accordion-collapse collapse"
                                data-bs-parent="#productInfo"
                            >
                                <div class="accordion-body px-0 text-secondary">
                                    Wash light and bright clothes separately. Do not use bleach. Dry in shade and iron on low heat when required.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button
                                    class="accordion-button collapsed px-0"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#deliveryInfo"
                                >
                                    Delivery & Cash on Delivery
                                </button>
                            </h2>

                            <div
                                id="deliveryInfo"
                                class="accordion-collapse collapse"
                                data-bs-parent="#productInfo"
                            >
                                <div class="accordion-body px-0 text-secondary">
                                    Pay when your order arrives. Delivery is free for orders of {{ number_format((float) config('store.free_shipping_threshold'), 0) }} {{ config('store.currency') }} or more. Orders below this amount have a flat {{ number_format((float) config('store.shipping_fee'), 0) }} {{ config('store.currency') }} delivery fee. Final delivery charges are shown in your cart and at checkout.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button
                                    class="accordion-button collapsed px-0"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#colourInfo"
                                >
                                    Colour disclaimer
                                </button>
                            </h2>

                            <div
                                id="colourInfo"
                                class="accordion-collapse collapse"
                                data-bs-parent="#productInfo"
                            >
                                <div class="accordion-body px-0 text-secondary">
                                    Colours may vary slightly from the product image because of lighting and screen settings.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @if ($related->isNotEmpty())
        <section class="py-5 bg-light">
            <div class="container">
                <div class="text-center mb-4">
                    <div class="small text-uppercase text-secondary fw-bold">
                        Complete the look
                    </div>

                    <h2 class="display-6 fw-normal">
                        You may also like
                    </h2>
                </div>

                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-lg-4">
                    @foreach ($related as $relatedProduct)
                        <div class="col d-flex">
                            @include(
                                'storefront.product-card',
                                ['product' => $relatedProduct]
                            )
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        const whatsappOrderButton = document.querySelector('[data-whatsapp-order]');
        const quantityInput = document.getElementById('quantity');

        if (whatsappOrderButton && quantityInput) {
            const updateWhatsAppOrderLink = () => {
                const requestedQuantity = Number(quantityInput.value);
                const quantity = Number.isInteger(requestedQuantity) && requestedQuantity > 0
                    ? Math.min(requestedQuantity, Number(quantityInput.max))
                    : 1;
                const subtotal = Number(whatsappOrderButton.dataset.productAmount) * quantity;
                const shippingFee = Number(whatsappOrderButton.dataset.shippingFee);
                const shippingThreshold = Number(whatsappOrderButton.dataset.freeShippingThreshold);
                const shipping = subtotal > 0 && subtotal < shippingThreshold ? shippingFee : 0;
                const formatAmount = (amount) => Math.round(amount).toLocaleString('en-US');
                const message = [
                    'Hello, I would like to order this product:',
                    whatsappOrderButton.dataset.productName,
                    `Quantity: ${quantity}`,
                    `Unit price: ${whatsappOrderButton.dataset.productPrice}`,
                    `Subtotal: ${formatAmount(subtotal)} ${whatsappOrderButton.dataset.productCurrency}`,
                    `Delivery: ${shipping > 0
                        ? `${formatAmount(shipping)} ${whatsappOrderButton.dataset.productCurrency}`
                        : 'Free'}`,
                    `Total: ${formatAmount(subtotal + shipping)} ${whatsappOrderButton.dataset.productCurrency}`,
                    ...(whatsappOrderButton.dataset.productPieceCount
                        ? [`Total pieces: ${whatsappOrderButton.dataset.productPieceCount}`]
                        : []),
                    ...(whatsappOrderButton.dataset.productPieces
                        ? [`Included pieces: ${whatsappOrderButton.dataset.productPieces}`]
                        : []),
                    `Product link: ${whatsappOrderButton.dataset.productUrl}`,
                ].join('\n');

                whatsappOrderButton.href = `https://wa.me/${whatsappOrderButton.dataset.whatsappNumber}?text=${encodeURIComponent(message)}`;
            };

            quantityInput.addEventListener('input', updateWhatsAppOrderLink);
            quantityInput.addEventListener('change', updateWhatsAppOrderLink);
        }

    </script>
@endpush
