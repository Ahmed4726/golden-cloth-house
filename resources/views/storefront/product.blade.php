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
    <section class="product-detail">
        <div class="gallery">
            @if ($firstImage)
                <div class="main-image">
                    <img id="product-main-image" src="{{ str_starts_with($firstImage, 'http') ? $firstImage : asset('storage/'.$firstImage) }}" alt="{{ $product->name }}">
                </div>

                @if (count($images) > 1)
                    <div class="thumbs" aria-label="Product gallery">
                        @foreach ($images as $index => $image)
                            <button
                                class="thumb {{ $index === 0 ? 'active' : '' }}"
                                type="button"
                                data-image="{{ str_starts_with($image, 'http') ? $image : asset('storage/'.$image) }}"
                                aria-label="View product image {{ $index + 1 }}"
                            >
                                <img src="{{ str_starts_with($image, 'http') ? $image : asset('storage/'.$image) }}" alt="{{ $product->name }} image {{ $index + 1 }}" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="main-image placeholder">{{ config('store.name') }}</div>
            @endif
        </div>

        <div class="details">
            <span class="eyebrow">{{ $product->category ?: 'COLLECTION' }}</span>
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
                <div class="stock">In stock · {{ $product->stock_quantity }} available</div>
            @else
                <div class="stock out">Currently out of stock</div>
            @endif

            <div class="description">{!! nl2br(e($product->description)) !!}</div>

            @if ($product->sku)
                <div class="sku">SKU: {{ $product->sku }}</div>
            @endif

            @if ($product->stock_quantity > 0)
                <form method="POST" action="{{ route('cart.add', $product) }}">
                    @csrf

                    <div class="quantity-row">
                        <div class="quantity">
                            <label class="form-label" for="quantity">Quantity</label>
                            <input class="input" id="quantity" type="number" name="quantity" value="1" min="1" max="{{ min($product->stock_quantity, 20) }}">
                        </div>

                        <button class="btn" type="submit">Add to Cart</button>
                    </div>
                </form>

                <div class="trust">
                    <strong>Cash on Delivery</strong><br>
                    Pay when your order arrives. Free delivery on orders over {{ number_format(config('store.free_shipping_threshold')) }} {{ config('store.currency') }}.
                </div>
            @endif
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">YOU MAY ALSO LIKE</span>
                    <h2>Related products</h2>
                </div>
            </div>

            <div class="product-grid">
                @foreach ($related as $relatedProduct)
                    @include('storefront.product-card', ['product' => $relatedProduct])
                @endforeach
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-image]').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById('product-main-image').src = button.dataset.image;
                document.querySelectorAll('.thumb').forEach((thumb) => thumb.classList.remove('active'));
                button.classList.add('active');
            });
        });
    </script>
@endpush
