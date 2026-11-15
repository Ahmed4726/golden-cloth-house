@extends('storefront.layout')

@php
    $title = config('store.name') . ' | Women & Men Unstitched Wear';
    $shareImage = $slides->first()?->imageUrl()
        ? url($slides->first()->imageUrl())
        : asset('images/golden-cloth-house-logo-header.svg');

    $description = 'Shop women and men unstitched wear at ' .
        config('store.name') .
        '. Discover printed and embroidered suits, seasonal fabrics and new arrivals with Cash on Delivery across Pakistan.';

    $schema = json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebSite',
                '@id' => url('/') . '#website',
                'name' => config('store.name'),
                'url' => url('/'),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => route('shop') . '?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            [
                '@type' => 'ClothingStore',
                '@id' => url('/') . '#store',
                'name' => config('store.name'),
                'url' => url('/'),
                'description' => $description,
                'areaServed' => 'Pakistan',
                'telephone' => config('store.contact_phone') ?: null,
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'Amin Market, Madina Bazar, Sargodha Road',
                    'addressLocality' => 'Gujrat',
                    'addressRegion' => 'Punjab',
                    'addressCountry' => 'PK',
                ],
                'sameAs' => array_values(array_filter([
                    config('store.facebook'),
                    config('store.instagram'),
                    config('store.tiktok'),
                    config('store.youtube'),
                ])),
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp

@section('content')
    <section
        id="homeHero"
        class="carousel slide"
        data-bs-ride="carousel"
        aria-label="Featured collection"
    >
        <div class="carousel-inner">
            @forelse ($slides as $index => $slide)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <div class="ratio ratio-21x9">
                        <div class="position-absolute top-0 start-0 w-100 h-100">
                            <img
                                class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover"
                                src="{{ $slide->imageUrl() ?: asset('images/product-placeholder.svg') }}"
                                alt="{{ $slide->title ?: config('store.name') }}"
                                loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
                            >

                            <div class="position-absolute bottom-0 start-0 d-flex align-items-center justify-content-between gap-2 w-100 p-2 p-sm-4 text-white">
                                <div class="min-w-0">
                                    @if ($slide->subtitle)
                                        <div class="small text-uppercase fw-semibold text-truncate">
                                            {{ $slide->subtitle }}
                                        </div>
                                    @endif

                                    @if ($slide->title)
                                        <h1 class="h5 fw-bold mb-0 text-break">
                                            {{ $slide->title }}
                                        </h1>
                                    @endif

                                    @if ($slide->description)
                                        <p class="small mb-0 mt-1 d-none d-lg-block">
                                            {{ $slide->description }}
                                        </p>
                                    @endif
                                </div>

                                @if ($slide->button_url)
                                    <a
                                        class="btn btn-light btn-sm text-uppercase fw-bold flex-shrink-0"
                                        href="{{ $slide->button_url }}"
                                    >
                                        {{ $slide->button_text ?: 'Shop now' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="carousel-item active">
                    <div class="bg-dark text-white py-5">
                        <div class="container py-5">
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="small text-uppercase fw-bold">
                                        Golden Cloth House
                                    </div>

                                    <h1 class="display-4 fw-normal">
                                        Unstitched. Elegant. Yours.
                                    </h1>

                                    <p class="lead">
                                        Explore refined women and men unstitched wear, from everyday fabrics to embroidered occasion pieces.
                                    </p>

                                    <a
                                        class="btn btn-light text-uppercase fw-bold"
                                        href="{{ route('shop') }}"
                                    >
                                        Shop the collection
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($slides->count() > 1)
            <div class="carousel-indicators position-static w-100 m-0 py-2 bg-white">
                @foreach ($slides as $index => $slide)
                    <button
                        type="button"
                        data-bs-target="#homeHero"
                        data-bs-slide-to="{{ $index }}"
                        class="bg-dark {{ $index === 0 ? 'active' : '' }}"
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $index + 1 }}"
                    ></button>
                @endforeach
            </div>
        @endif

        @if ($slides->count() > 1)
            <button
                class="carousel-control-prev"
                type="button"
                data-bs-target="#homeHero"
                data-bs-slide="prev"
                aria-label="Previous slide"
            >
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                <span class="visually-hidden">Previous</span>
            </button>

            <button
                class="carousel-control-next"
                type="button"
                data-bs-target="#homeHero"
                data-bs-slide="next"
                aria-label="Next slide"
            >
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                <span class="visually-hidden">Next</span>
            </button>
        @endif
    </section>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-4 mb-lg-5">
                <div class="small text-uppercase text-secondary fw-bold">
                    Shop by collection
                </div>

                <h2 class="display-6 fw-normal">
                    Made for Pakistani wardrobes
                </h2>

                <p class="text-secondary mx-auto mb-0 col-lg-7">
                    Focused collections for women and men unstitched wear, organized around pieces, fabrics and occasions.
                </p>
            </div>

            <div class="row g-3">
                @forelse ($categoryHighlights as $highlight)
                    @php
                        $category = $highlight['category'];
                        $categoryProduct = $highlight['product'];
                        $image = $categoryProduct?->images()[0] ?? null;
                    @endphp

                    <div class="col-6 col-lg-3 d-flex">
                        <a
                            class="card h-100 w-100 text-decoration-none text-dark border-0 shadow-sm overflow-hidden"
                            href="{{ route('shop.collection', $category->slug) }}"
                        >
                            <div class="ratio ratio-1x1 bg-light">
                                <img
                                    class="w-100 h-100 object-fit-contain"
                                    src="{{ $image ? $categoryProduct->imageUrl($image) : asset('images/product-placeholder.svg') }}"
                                    alt="{{ $category->name }}"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
                                >
                            </div>

                            <div class="card-body">
                                <small class="text-uppercase text-secondary">
                                    {{ $category->description ?: 'Collection' }}
                                </small>

                                <h3 class="h5 mb-0 text-truncate">
                                    {{ $category->name }}
                                </h3>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-6 col-lg-3 d-flex">
                        <a
                            class="card h-100 w-100 bg-dark text-white text-decoration-none border-0"
                            href="{{ route('shop', ['category' => 'women-unstitched']) }}"
                        >
                            <div class="ratio ratio-1x1">
                                <div class="d-flex flex-column justify-content-end p-4">
                                    <small>Women</small>
                                    <h3 class="h4 mb-0">Women Unstitched</h3>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-6 col-lg-3 d-flex">
                        <a
                            class="card h-100 w-100 bg-dark text-white text-decoration-none border-0"
                            href="{{ route('shop', ['category' => 'men-unstitched']) }}"
                        >
                            <div class="ratio ratio-1x1">
                                <div class="d-flex flex-column justify-content-end p-4">
                                    <small>Men</small>
                                    <h3 class="h4 mb-0">Men Unstitched</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    @if ($brands->isNotEmpty())
        <section class="py-5 bg-light">
            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
                    <div>
                        <div class="small text-uppercase text-secondary fw-bold">Our labels</div>
                        <h2 class="display-6 fw-normal mb-0">Shop by brand</h2>
                    </div>
                    <a class="link-dark fw-bold text-uppercase small" href="{{ route('shop') }}">Explore all products</a>
                </div>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                    @foreach ($brands as $brand)
                        @php
                            $brandProduct = $brand->products->first();
                            $brandImage = $brandProduct?->images()[0] ?? null;
                        @endphp
                        <div class="col">
                            <a class="card h-100 text-decoration-none text-dark border-0 shadow-sm overflow-hidden" href="{{ route('shop.brand', $brand->slug) }}">
                                <div class="ratio ratio-1x1 bg-white">
                                    <img class="w-100 h-100 object-fit-contain" src="{{ $brandImage ? $brandProduct->imageUrl($brandImage) : asset('images/product-placeholder.svg') }}" alt="{{ $brand->name }}" loading="lazy">
                                </div>
                                <div class="card-body d-flex justify-content-between align-items-center gap-2">
                                    <h3 class="h5 mb-0 text-truncate">{{ $brand->name }}</h3>
                                    <span class="small text-secondary">Shop <i class="fa-solid fa-arrow-right"></i></span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($featuredProducts->isNotEmpty())
        <section class="py-5">
            <div class="container">
                <div class="text-center mb-4">
                    <div class="small text-uppercase text-secondary fw-bold">Handpicked for you</div>
                    <h2 class="display-6 fw-normal">Featured pieces</h2>
                </div>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-lg-4">
                    @foreach ($featuredProducts as $product)
                        <div class="col d-flex">@include('storefront.product-card', ['product' => $product])</div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($saleProducts->isNotEmpty())
        <section class="py-5 bg-light">
            <div class="container">
                <div class="text-center mb-4">
                    <div class="small text-uppercase text-secondary fw-bold">Limited-time offers</div>
                    <h2 class="display-6 fw-normal">On sale</h2>
                </div>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-lg-4">
                    @foreach ($saleProducts as $product)
                        <div class="col d-flex">@include('storefront.product-card', ['product' => $product])</div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-4 mb-lg-5">
                <div class="small text-uppercase text-secondary fw-bold">
                    Just landed
                </div>

                <h2 class="display-6 fw-normal">
                    New arrivals
                </h2>

                <p class="text-secondary mx-auto col-lg-7">
                    Fresh pieces added to the store, ready to order with Cash on Delivery.
                </p>

                <a
                    class="link-dark fw-bold text-uppercase small"
                    href="{{ route('shop', ['sort' => 'newest']) }}"
                >
                    View all new arrivals
                </a>
            </div>

            @if ($products->isNotEmpty())
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-lg-4">
                    @foreach ($products as $product)
                        <div class="col d-flex">
                            @include(
                                'storefront.product-card',
                                ['product' => $product]
                            )
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-light border text-center">
                    Products will appear here as you add them from the admin panel.
                </div>
            @endif
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-4">
                <div class="small text-uppercase text-secondary fw-bold">
                    Shop your way
                </div>

                <h2 class="display-6 fw-normal">
                    Women & Men · Unstitched only
                </h2>
            </div>

            <div class="row g-3">
                @php
                    $quickLinks = [
                        [
                            'label' => 'Women Unstitched',
                            'small' => 'Women',
                            'url' => route(
                                'shop',
                                ['category' => 'women-unstitched']
                            ),
                        ],
                        [
                            'label' => 'Men Unstitched',
                            'small' => 'Men',
                            'url' => route(
                                'shop',
                                ['category' => 'men-unstitched']
                            ),
                        ],
                        [
                            'label' => 'Embroidered',
                            'small' => 'Occasion',
                            'url' => route(
                                'shop',
                                ['q' => 'Embroidered']
                            ),
                        ],
                        [
                            'label' => 'Printed',
                            'small' => 'Everyday',
                            'url' => route(
                                'shop',
                                ['q' => 'Printed']
                            ),
                        ],
                    ];
                @endphp

                @foreach ($quickLinks as $link)
                    <div class="col-6 col-lg-3 d-flex">
                        <a
                            class="card h-100 w-100 bg-dark text-white text-decoration-none border-0"
                            href="{{ $link['url'] }}"
                        >
                            <div class="ratio ratio-1x1">
                                <div class="d-flex flex-column justify-content-end p-4">
                                    <small class="text-uppercase">
                                        {{ $link['small'] }}
                                    </small>

                                    <h3 class="h4 mb-0">
                                        {{ $link['label'] }}
                                    </h3>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
