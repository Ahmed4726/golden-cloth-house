@extends('storefront.layout')

@php
    $images = $product->images();
    $firstImage = $images[0] ?? null;
    $shareImage = $firstImage
        ? (str_starts_with($firstImage, 'http') ? $firstImage : asset('storage/'.$firstImage))
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
        'image' => array_map(fn ($image) => str_starts_with($image, 'http') ? $image : asset('storage/'.$image), $images),
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
    <div class="container">
        <div class="breadcrumbs" style="padding-top:25px">
            <a href="{{ route('home') }}">Home</a> / <a href="{{ route('shop') }}">Shop</a> / {{ $product->name }}
        </div>

        <section class="product-detail">
            <div class="product-gallery">
                @if ($firstImage)
                    <div class="main-image">
                        <img id="product-main-image" src="{{ $shareImage }}" alt="{{ $product->name }}">
                    </div>

                    @if (count($images) > 1)
                        <div class="thumbs" aria-label="Product gallery">
                            @foreach ($images as $index => $image)
                                @php($imageUrl = str_starts_with($image, 'http') ? $image : asset('storage/'.$image))
                                <button class="thumb {{ $index === 0 ? 'active' : '' }}" type="button" data-image="{{ $imageUrl }}" aria-label="View product image {{ $index + 1 }}">
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }} image {{ $index + 1 }}" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="main-image placeholder">{{ config('store.name') }}</div>
                @endif
            </div>

            <div class="details">
                <span class="eyebrow">{{ $product->categoryRelation?->name ?: ($product->category ?: 'UNSTITCHED') }}</span>
                <h1>{{ $product->name }}</h1>

                @if ($product->price)
                    <div class="price">
                        {{ number_format((float) $product->price, 0) }} {{ $product->currency }}
                        @if ($product->compare_at_price && $product->compare_at_price > $product->price)
                            <span class="compare">{{ number_format((float) $product->compare_at_price, 0) }} {{ $product->currency }}</span>
                        @endif
                    </div>
                @endif

                @if ($product->stock_quantity > 0)
                    <div class="stock">In stock · Ready to ship</div>
                @else
                    <div class="stock out">Currently out of stock</div>
                @endif

                <div class="description">{!! nl2br(e($product->description)) !!}</div>

                @if ($product->stock_quantity > 0)
                    <form method="POST" action="{{ route('cart.add', $product) }}">
                        @csrf
                        <div class="quantity-row">
                            <div class="quantity">
                                <label class="form-label" for="quantity">Quantity</label>
                                <input class="input" id="quantity" type="number" name="quantity" value="1" min="1" max="{{ min($product->stock_quantity, 20) }}">
                            </div>
                            <button class="btn" type="submit">Add to bag</button>
                        </div>
                    </form>

                    <div class="trust-grid">
                        <div class="trust-item">Cash on Delivery</div>
                        <div class="trust-item">Pakistan-wide delivery</div>
                        <div class="trust-item">Secure checkout</div>
                    </div>
                @endif

                <div class="product-meta">
                    @if ($product->sku)
                        <div class="accordion">
                            <details>
                                <summary>Product details</summary>
                                <div class="accordion-content">SKU: {{ $product->sku }}</div>
                            </details>
                        </div>
                    @endif
                    <div class="accordion">
                        <details>
                            <summary>Care instructions</summary>
                            <div class="accordion-content">Wash light and bright clothes separately. Do not use bleach. Dry in shade and iron on low heat when required.</div>
                        </details>
                    </div>
                    <div class="accordion">
                        <details>
                            <summary>Delivery & Cash on Delivery</summary>
                            <div class="accordion-content">Pay when your order arrives. Delivery charges and free-shipping eligibility are shown at checkout.</div>
                        </details>
                    </div>
                    <div class="accordion">
                        <details>
                            <summary>Colour disclaimer</summary>
                            <div class="accordion-content">Colours may vary slightly from the product image because of lighting and screen settings.</div>
                        </details>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @if ($related->isNotEmpty())
        <section class="section alt">
            <div class="container">
                <div class="section-head">
                    <span class="section-kicker">Complete the look</span>
                    <h2>You may also like</h2>
                </div>
                <div class="product-grid">
                    @foreach ($related as $relatedProduct)
                        @include('storefront.product-card', ['product' => $relatedProduct])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

<style>
    .product-detail .main-image {
        background: #f7f5f1;
    }

    .product-detail .main-image img,
    .product-detail .thumb img {
        object-fit: contain;
        background: #f7f5f1;
    }
</style>

@push('scripts')
    <script>
        document.querySelectorAll('[data-image]').forEach((button) => {
            button.addEventListener('click', () => {
                const mainImage = document.getElementById('product-main-image');
                if (mainImage) mainImage.src = button.dataset.image;
                document.querySelectorAll('.thumb').forEach((thumb) => thumb.classList.remove('active'));
                button.classList.add('active');
            });
        });
    </script>
@endpush
