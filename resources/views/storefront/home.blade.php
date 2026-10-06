@extends('storefront.layout')

@php
    $title = config('store.name').' | Women & Men Unstitched Wear';
    $description = 'Shop women and men unstitched wear at '.config('store.name').'. Discover printed and embroidered suits, seasonal fabrics and new arrivals with Cash on Delivery across Pakistan.';
    $schema = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'ClothingStore',
        'name' => config('store.name'),
        'url' => url('/'),
        'description' => $description,
        'areaServed' => 'Pakistan',
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp

@section('content')
    <section class="hero" aria-label="Featured collection">
        @forelse ($slides as $index => $slide)
            <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" data-hero-slide>
                @if ($slide->image_path)
                    <img class="hero-image" src="{{ asset('storage/'.$slide->image_path) }}" alt="{{ $slide->title }}">
                @endif
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    @if ($slide->subtitle)
                        <span class="hero-eyebrow">{{ $slide->subtitle }}</span>
                    @endif
                    <h1>{{ $slide->title }}</h1>
                    @if ($slide->description)
                        <p>{{ $slide->description }}</p>
                    @endif
                    @if ($slide->button_url)
                        <div><a class="btn light" href="{{ $slide->button_url }}">{{ $slide->button_text ?: 'Shop now' }}</a></div>
                    @endif
                </div>
            </div>
        @empty
            <div class="hero-slide active">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <span class="hero-eyebrow">GOLDEN CLOTH HOUSE</span>
                    <h1>Unstitched. Elegant. Yours.</h1>
                    <p>Explore refined women and men unstitched wear, from everyday fabrics to embroidered occasion pieces.</p>
                    <div><a class="btn light" href="{{ route('shop') }}">Shop the collection</a></div>
                </div>
            </div>
        @endforelse

        @if ($slides->count() > 1)
            <div class="hero-dots" aria-label="Slider controls">
                @foreach ($slides as $index => $slide)
                    <button class="hero-dot {{ $index === 0 ? 'active' : '' }}" type="button" data-hero-dot="{{ $index }}" aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>
        @endif
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <span class="section-kicker">Shop by collection</span>
                <h2>Made for Pakistani wardrobes</h2>
                <p>Focused collections for women and men unstitched wear, organized around pieces, fabrics and occasions.</p>
            </div>

            <div class="collection-grid">
                @forelse ($categoryHighlights as $highlight)
                    @php
                        $category = $highlight['category'];
                        $image = $highlight['product']?->images()[0] ?? null;
                    @endphp
                    <a class="collection-card" href="{{ route('shop', ['category' => $category->slug]) }}">
                        @if ($image)
                            <img src="{{ str_starts_with($image, 'http') ? $image : asset('storage/'.$image) }}" alt="{{ $category->name }}" loading="lazy">
                        @endif
                        <div class="collection-copy">
                            <small>{{ $category->description ?: 'Collection' }}</small>
                            <h3>{{ $category->name }}</h3>
                        </div>
                    </a>
                @empty
                    <a class="collection-card" href="{{ route('shop', ['category' => 'women-unstitched']) }}">
                        <div class="collection-copy"><small>Women</small><h3>Women Unstitched</h3></div>
                    </a>
                    <a class="collection-card" href="{{ route('shop', ['category' => 'men-unstitched']) }}">
                        <div class="collection-copy"><small>Men</small><h3>Men Unstitched</h3></div>
                    </a>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section alt">
        <div class="container">
            <div class="section-head">
                <span class="section-kicker">Just landed</span>
                <h2>New arrivals</h2>
                <p>Fresh pieces added to the store, ready to order with Cash on Delivery.</p>
                <div style="margin-top:18px"><a class="text-link" href="{{ route('shop', ['sort' => 'newest']) }}">View all new arrivals</a></div>
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
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <span class="section-kicker">Shop your way</span>
                <h2>Women & Men · Unstitched only</h2>
            </div>
            <div class="collection-grid">
                <a class="collection-card" href="{{ route('shop', ['category' => 'women-unstitched']) }}">
                    <div class="collection-copy"><small>Women</small><h3>Women Unstitched</h3></div>
                </a>
                <a class="collection-card" href="{{ route('shop', ['category' => 'men-unstitched']) }}">
                    <div class="collection-copy"><small>Men</small><h3>Men Unstitched</h3></div>
                </a>
                <a class="collection-card" href="{{ route('shop', ['q' => 'Embroidered']) }}">
                    <div class="collection-copy"><small>Occasion</small><h3>Embroidered</h3></div>
                </a>
                <a class="collection-card" href="{{ route('shop', ['q' => 'Printed']) }}">
                    <div class="collection-copy"><small>Everyday</small><h3>Printed</h3></div>
                </a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const slides = [...document.querySelectorAll('[data-hero-slide]')];
            const dots = [...document.querySelectorAll('[data-hero-dot]')];

            if (slides.length < 2) return;

            let current = 0;

            const show = (index) => {
                slides.forEach((slide, slideIndex) => {
                    slide.classList.toggle('active', slideIndex === index);
                });
                dots.forEach((dot, dotIndex) => {
                    dot.classList.toggle('active', dotIndex === index);
                });
                current = index;
            };

            dots.forEach((dot) => {
                dot.addEventListener('click', () => show(Number(dot.dataset.heroDot)));
            });

            setInterval(() => show((current + 1) % slides.length), 5500);
        });
    </script>
@endpush
