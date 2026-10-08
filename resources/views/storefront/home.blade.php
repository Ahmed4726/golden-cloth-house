@extends('storefront.layout')

@php
    $title = config('store.name') . ' | Women & Men Unstitched Wear';

    $description = 'Shop women and men unstitched wear at ' .
        config('store.name') .
        '. Discover printed and embroidered suits, seasonal fabrics and new arrivals with Cash on Delivery across Pakistan.';

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
    <section
        id="homeHero"
        class="carousel slide"
        data-bs-ride="carousel"
        aria-label="Featured collection"
    >
        @if ($slides->count() > 1)
            <div class="carousel-indicators">
                @foreach ($slides as $index => $slide)
                    <button
                        type="button"
                        data-bs-target="#homeHero"
                        data-bs-slide-to="{{ $index }}"
                        class="{{ $index === 0 ? 'active' : '' }}"
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $index + 1 }}"
                    ></button>
                @endforeach
            </div>
        @endif

        <div class="carousel-inner">
            @forelse ($slides as $index => $slide)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img
                        class="d-block w-100 h-auto"
                        src="{{ $slide->imageUrl() ?: asset('images/product-placeholder.svg') }}"
                        alt="{{ $slide->title ?: config('store.name') }}"
                        loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                        onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
                    >

                    <div class="bg-dark text-white text-center py-3 py-md-4">
                        <div class="container">
                            @if ($slide->subtitle)
                                <div class="small text-uppercase fw-bold">
                                    {{ $slide->subtitle }}
                                </div>
                            @endif

                            @if ($slide->title)
                                <h1 class="h2 fw-normal mb-2">
                                    {{ $slide->title }}
                                </h1>
                            @endif

                            @if ($slide->description)
                                <p class="lead mb-3 d-none d-md-block">
                                    {{ $slide->description }}
                                </p>
                            @endif

                            @if ($slide->button_url)
                                <a
                                    class="btn btn-light text-uppercase fw-bold"
                                    href="{{ $slide->button_url }}"
                                >
                                    {{ $slide->button_text ?: 'Shop now' }}
                                </a>
                            @endif
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
            <button
                class="carousel-control-prev"
                type="button"
                data-bs-target="#homeHero"
                data-bs-slide="prev"
                aria-label="Previous slide"
            >
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>

            <button
                class="carousel-control-next"
                type="button"
                data-bs-target="#homeHero"
                data-bs-slide="next"
                aria-label="Next slide"
            >
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
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
                            href="{{ route('shop', ['category' => $category->slug]) }}"
                        >
                            <div class="ratio ratio-3x4 bg-light">
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
                            <div class="ratio ratio-3x4">
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
                            <div class="ratio ratio-3x4">
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
                            <div class="ratio ratio-3x4">
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
