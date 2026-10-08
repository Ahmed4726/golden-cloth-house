@extends('storefront.layout')

@php
    $images = $product->images();
    $firstImage = $images[0] ?? null;
    $shareImage = $firstImage
        ? (str_starts_with($firstImage, 'http') ? $firstImage : $product->imageUrl($firstImage))
        : null;
    $title = $product->name.' | '.config('store.name');
    $description = \Illuminate\Support\Str::limit(strip_tags($product->description ?: 'Shop '.$product->name.' from '.config('store.name').'. Cash on Delivery available across Pakistan.'), 155);
    $schema = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => strip_tags($product->description ?: ''),
        'sku' => $product->sku,
        'category' => $product->category,
        'image' => array_map(
            fn ($image) => str_starts_with($image, 'http') ? $image : $product->imageUrl($image),
            $images
        ),
        'offers' => [
            '@type' => 'Offer',
            'priceCurrency' => $product->currency,
            'price' => (float) $product->price,
            'availability' => $product->stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => route('shop.product', $product),
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp

@section('content')
    <div class="container py-4">
        <nav class="small text-secondary mb-4" aria-label="Breadcrumb">
            <a class="text-dark text-decoration-none" href="{{ route('home') }}">Home</a>
            <span class="mx-1">/</span>
            <a class="text-dark text-decoration-none" href="{{ route('shop') }}">Shop</a>
            <span class="mx-1">/</span>
            <span>{{ $product->name }}</span>
        </nav>

        <section class="row g-4 g-lg-5">
            <div class="col-lg-7">
                @if ($firstImage && $product->imageExists($firstImage))
                    <div class="ratio ratio-4x5 bg-light rounded overflow-hidden">
                        <img
                            id="product-main-image"
                            class="w-100 h-100 object-fit-contain bg-white"
                            src="{{ $shareImage }}"
                            alt="{{ $product->name }}"
                        >
                    </div>

                    @if (count($images) > 1)
                        <div class="row row-cols-4 row-cols-sm-5 row-cols-md-6 g-2 mt-2" aria-label="Product gallery">
                            @foreach ($images as $index => $image)
                                @php
                                    $imageUrl = str_starts_with($image, 'http') ? $image : $product->imageUrl($image);
                                    $imageAvailable = $product->imageExists($image);
                                @endphp
                                @if ($imageAvailable)
                                    <div class="col">
                                        <button
                                            class="btn p-0 border {{ $index === 0 ? 'border-dark' : '' }} w-100 ratio ratio-3x4 overflow-hidden"
                                            type="button"
                                            data-image="{{ $imageUrl }}"
                                            aria-label="View product image {{ $index + 1 }}"
                                        >
                                            <img
                                                class="w-100 h-100 object-fit-contain bg-light"
                                                src="{{ $imageUrl }}"
                                                alt="{{ $product->name }} image {{ $index + 1 }}"
                                                loading="lazy"
                                            >
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="ratio ratio-4x5 bg-light d-flex align-items-center justify-content-center text-secondary">
                        <div class="text-center">
                            <i class="bi bi-image fs-1 d-block mb-2"></i>
                            Product image unavailable
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-5">
                <div>
                    <div class="small text-uppercase text-secondary fw-bold">
                        {{ $product->categoryRelation?->name ?: ($product->category ?: 'UNSTITCHED') }}
                    </div>
                    <h1 class="display-6 fw-normal mt-2">{{ $product->name }}</h1>

                    @if ($product->price)
                        <div class="fs-4 fw-bold mb-2">
                            {{ number_format((float) $product->price, 0) }} {{ $product->currency }}
                            @if ($product->compare_at_price && $product->compare_at_price > $product->price)
                                <span class="fs-6 text-secondary text-decoration-line-through ms-2">
                                    {{ number_format((float) $product->compare_at_price, 0) }} {{ $product->currency }}
                                </span>
                            @endif
                        </div>
                    @endif

                    @if ($product->stock_quantity > 0)
                        <div class="small text-success text-uppercase fw-bold">In stock · Ready to ship</div>
                    @else
                        <div class="small text-danger text-uppercase fw-bold">Currently out of stock</div>
                    @endif

                    <div class="text-secondary my-4">{!! nl2br(e($product->description)) !!}</div>

                    @if ($product->stock_quantity > 0)
                        <form method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <div class="col-4 col-sm-3">
                                    <label class="form-label small fw-bold text-uppercase" for="quantity">Quantity</label>
                                    <input class="form-control" id="quantity" type="number" name="quantity" value="1" min="1" max="{{ min($product->stock_quantity, 20) }}">
                                </div>
                                <div class="col">
                                    <button class="btn btn-dark w-100 py-2 text-uppercase fw-bold" type="submit">Add to bag</button>
                                </div>
                            </div>
                        </form>

                        <div class="row row-cols-3 g-2 mt-3">
                            <div class="col"><div class="bg-light p-3 text-center small h-100">Cash on Delivery</div></div>
                            <div class="col"><div class="bg-light p-3 text-center small h-100">Pakistan-wide delivery</div></div>
                            <div class="col"><div class="bg-light p-3 text-center small h-100">Secure checkout</div></div>
                        </div>
                    @endif

                    <div class="accordion accordion-flush border-top mt-4" id="productInfo">
                        @if ($product->sku)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#productDetails">Product details</button>
                                </h2>
                                <div id="productDetails" class="accordion-collapse collapse" data-bs-parent="#productInfo">
                                    <div class="accordion-body px-0 text-secondary">SKU: {{ $product->sku }}</div>
                                </div>
                            </div>
                        @endif
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#careInfo">Care instructions</button>
                            </h2>
                            <div id="careInfo" class="accordion-collapse collapse" data-bs-parent="#productInfo">
                                <div class="accordion-body px-0 text-secondary">Wash light and bright clothes separately. Do not use bleach. Dry in shade and iron on low heat when required.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#deliveryInfo">Delivery & Cash on Delivery</button>
                            </h2>
                            <div id="deliveryInfo" class="accordion-collapse collapse" data-bs-parent="#productInfo">
                                <div class="accordion-body px-0 text-secondary">Pay when your order arrives. Delivery charges and free-shipping eligibility are shown at checkout.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#colourInfo">Colour disclaimer</button>
                            </h2>
                            <div id="colourInfo" class="accordion-collapse collapse" data-bs-parent="#productInfo">
                                <div class="accordion-body px-0 text-secondary">Colours may vary slightly from the product image because of lighting and screen settings.</div>
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
                    <div class="small text-uppercase text-secondary fw-bold">Complete the look</div>
                    <h2 class="display-6 fw-normal">You may also like</h2>
                </div>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-lg-4">
                    @foreach ($related as $relatedProduct)
                        <div class="col d-flex">
                            @include('storefront.product-card', ['product' => $relatedProduct])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-image]').forEach((button) => {
            button.addEventListener('click', () => {
                const mainImage = document.getElementById('product-main-image');

                if (mainImage) {
                    mainImage.src = button.dataset.image;
                }

                document.querySelectorAll('[data-image]').forEach((thumb) => {
                    thumb.classList.remove('border-dark');
                });

                button.classList.add('border-dark');
            });
        });
    </script>
@endpush
