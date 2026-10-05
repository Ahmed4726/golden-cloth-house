@extends('storefront.layout')

@php
    $schema = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'ClothingStore',
        'name' => config('store.name'),
        'url' => url('/'),
        'description' => 'Clothing store with Cash on Delivery across Pakistan.',
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp

@section('content')
    <section class="hero">
        @if ($slides->isNotEmpty())
            @foreach ($slides as $index => $slide)
                <div class="hero-slide" style="position:absolute;inset:0;display:{{ $index === 0 ? 'block' : 'none' }}" data-slide>
                    @if ($slide->image_path)
                        <img class="hero-image" src="{{ asset('storage/'.$slide->image_path) }}" alt="{{ $slide->title }}">
                    @endif
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        @if ($slide->subtitle)
                            <span class="eyebrow">{{ $slide->subtitle }}</span>
                        @endif
                        <h1>{{ $slide->title }}</h1>
                        @if ($slide->description)
                            <p>{{ $slide->description }}</p>
                        @endif
                        @if ($slide->button_url)
                            <a class="shop-btn" href="{{ $slide->button_url }}">{{ $slide->button_text ?: 'Shop now' }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="hero-content">
                <span class="eyebrow">GOLDEN CLOTH HOUSE</span>
                <h1>Elegant clothing, made for your everyday style.</h1>
                <p>Discover our latest collection with Cash on Delivery available across Pakistan.</p>
                <a class="shop-btn" href="{{ route('shop') }}">Shop the collection</a>
            </div>
        @endif
    </section>

    @if ($slides->count() > 1)
        @push('scripts')
            <script>
                (() => {
                    const slides = [...document.querySelectorAll('[data-slide]')];
                    let index = 0;
                    setInterval(() => {
                        slides[index].style.display = 'none';
                        index = (index + 1) % slides.length;
                        slides[index].style.display = 'block';
                    }, 5000);
                })();
            </script>
        @endpush
    @endif

    <section class="section">
        <div class="section-head">
            <div>
                <span class="eyebrow">LATEST COLLECTION</span>
                <h2>New arrivals</h2>
            </div>
            <a href="{{ route('shop') }}">View all →</a>
        </div>

        @if ($products->isNotEmpty())
            <div class="product-grid">
                @foreach ($products as $product)
                    @include('storefront.product-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <div class="empty">Products will appear here as you add them from the admin panel.</div>
        @endif
    </section>

    @if ($categories->isNotEmpty())
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">SHOP BY STYLE</span>
                    <h2>Collections</h2>
                </div>
            </div>

            <div class="product-grid">
                @foreach ($categories as $category)
                    <a class="product-card" href="{{ route('shop', ['category' => $category]) }}" style="padding:28px;min-height:150px;display:flex;align-items:end;">
                        <div>
                            <span class="eyebrow">COLLECTION</span>
                            <h3 style="margin:7px 0 0;font-size:22px;">{{ $category }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
