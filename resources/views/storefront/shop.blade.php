@extends('storefront.layout')

@php
    $title = $collection
        ? $collection->name.' | '.config('store.name')
        : ($brandCollection ? $brandCollection->name.' | '.config('store.name') : 'Shop | '.config('store.name'));
    $description = \Illuminate\Support\Str::limit(
        strip_tags($collection?->description ?: $brandCollection?->description ?: 'Shop women and men unstitched wear from '.config('store.name').'. Filter by category, fabric, price, availability and newness.'),
        155
    );
    $schema = $collection || $brandCollection ? json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => $collection?->name ?: $brandCollection->name,
        'description' => $description,
        'url' => $canonical,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
@endphp

@section('content')
    <section class="bg-body-tertiary py-5">
        <div class="container">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-6 text-center text-lg-start">
                    <div class="small text-uppercase fw-bold text-secondary mb-3">
                        {{ $collection || $brandCollection ? 'A closer look at' : 'Thoughtfully chosen for you' }}
                    </div>
                    <h1 class="display-4 fw-normal mb-3">
                        {{ $collection?->name ?: ($brandCollection?->name ?: 'Find fabric you’ll love wearing.') }}
                    </h1>
                    <p class="lead text-secondary mb-4">
                        {{ $collection?->description ?: ($brandCollection?->description ?: 'Explore unstitched styles with beautiful prints, considered details and fabrics for every day and every occasion. Discover your next favorite from Golden Cloth House.') }}
                    </p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-2 mb-4">
                        <a class="btn btn-dark btn-lg px-4" href="#shop-products">
                            Explore the collection
                            <i class="fa-solid fa-arrow-down ms-2 small" aria-hidden="true"></i>
                        </a>
                        <a class="btn btn-outline-dark btn-lg px-4" href="{{ route('about') }}">Our story</a>
                    </div>

                    @if ($categories->isNotEmpty())
                        <nav class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-2" aria-label="Browse collections">
                            @foreach ($categories->take(5) as $category)
                                <a class="btn btn-sm btn-outline-secondary rounded-pill" href="{{ route('shop.collection', $category->slug) }}">
                                    {{ $category->name }}
                                    <i class="fa-solid fa-arrow-up-right-from-square small" aria-hidden="true"></i>
                                </a>
                            @endforeach
                        </nav>
                    @endif
                </div>

                <div class="col-lg-6">
                    <div class="ratio ratio-4x3 bg-dark rounded-3 shadow overflow-hidden" aria-label="Featured collection">
                        @if (! $collection && ! $brandCollection && $dailyFeaturedCollection)
                            @php
                                $featuredProduct = $dailyFeaturedCollection['product'];
                            @endphp
                            <a class="d-block w-100 h-100 text-white text-decoration-none" href="{{ route('shop.product', $featuredProduct->slug) }}" aria-label="View {{ $featuredProduct->name }}">
                                <img
                                    class="w-100 h-100 object-fit-cover"
                                    src="{{ $dailyFeaturedCollection['image'] }}"
                                    alt="{{ $featuredProduct->name }}"
                                    loading="eager"
                                >
                                <span class="badge text-bg-light position-absolute top-0 end-0 m-3 rounded-pill shadow">
                                    Today’s pick
                                </span>
                                <span class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-75 text-white p-3 p-lg-4">
                                    <span class="small text-uppercase fw-semibold">Featured collection</span>
                                    <span class="d-block h3 fw-semibold mb-0">{{ $featuredProduct->name }}</span>
                                    @if ($featuredProduct->categoryRelation)
                                        <span class="small">{{ $featuredProduct->categoryRelation->name }}</span>
                                    @endif
                                </span>
                            </a>
                        @elseif ($heroSlide?->imageUrl())
                            <a class="d-block w-100 h-100 text-white text-decoration-none" href="{{ $heroSlide->button_url ?: '#shop-products' }}" aria-label="{{ $heroSlide->title ?: 'Explore the featured collection' }}">
                                <img
                                    class="w-100 h-100 object-fit-cover"
                                    src="{{ $heroSlide->imageUrl() }}"
                                    alt="{{ $heroSlide->title ?: 'Featured collection from '.config('store.name') }}"
                                    loading="eager"
                                >
                                <span class="badge text-bg-light position-absolute top-0 end-0 m-3 rounded-pill shadow">
                                    30+ years
                                </span>
                                <span class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-75 text-white p-3 p-lg-4">
                                    <span class="small text-uppercase fw-semibold">Featured collection</span>
                                    @if ($heroSlide->title)
                                        <span class="d-block h3 fw-semibold mb-0">{{ $heroSlide->title }}</span>
                                    @endif
                                </span>
                            </a>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center text-center text-white p-4">
                                <span class="badge text-bg-light rounded-pill mb-3">30+ years</span>
                                <i class="fa-solid fa-wand-magic-sparkles fs-1 mb-3" aria-hidden="true"></i>
                                <span class="small text-uppercase fw-semibold">Golden Cloth House</span>
                                <span class="h3 fw-normal mb-0">Curated with care</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="border-bottom" aria-label="Shopping benefits">
        <div class="container py-3">
            <div class="row row-cols-1 row-cols-sm-3 g-3">
                <div class="col">
                    <div class="d-flex align-items-center justify-content-center gap-3 h-100">
                        <span class="rounded-circle bg-body-secondary text-dark p-3"><i class="fa-solid fa-award" aria-hidden="true"></i></span>
                        <span><strong class="d-block small">30+ years of experience</strong><span class="small text-secondary">Serving Gujrat with care</span></span>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex align-items-center justify-content-center gap-3 h-100">
                        <span class="rounded-circle bg-body-secondary text-dark p-3"><i class="fa-solid fa-truck-fast" aria-hidden="true"></i></span>
                        <span><strong class="d-block small">Delivery across Pakistan</strong><span class="small text-secondary">Your favorites, delivered</span></span>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex align-items-center justify-content-center gap-3 h-100">
                        <span class="rounded-circle bg-body-secondary text-dark p-3"><i class="fa-solid fa-box-open" aria-hidden="true"></i></span>
                        <span><strong class="d-block small">Free delivery from PKR 5,000</strong><span class="small text-secondary">Easy cash-on-delivery checkout</span></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pb-5">
        <div class="container py-5" id="shop-products">
            <div class="d-flex justify-content-between align-items-end gap-3 mb-4">
                <div>
                    <div class="small text-uppercase fw-bold text-secondary mb-2">Made for your wardrobe</div>
                    <h2 class="h2 fw-normal mb-1">{{ $collection || $brandCollection ? 'Explore this collection' : 'Explore our collection' }}</h2>
                    <div class="text-secondary small">
                        {{ $products->total() }} {{ \Illuminate\Support\Str::plural('piece', $products->total()) }}
                        @if ($filter['sale']) · Sale @endif
                    </div>
                </div>
                <button class="btn btn-outline-dark d-lg-none flex-shrink-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#shopFilters" aria-controls="shopFilters">
                    Filter & sort
                </button>
            </div>

            <div class="row g-4">
                <aside class="col-lg-3 d-none d-lg-block">
                    @include('storefront.shop-filters')
                </aside>

                <div class="col-lg-9">
                    @if ($products->isNotEmpty())
                        <div class="d-none d-lg-flex justify-content-end text-secondary small mb-3">
                            Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}
                        </div>
                        <div class="row row-cols-2 row-cols-md-3 g-3 g-lg-4">
                            @foreach ($products as $product)
                                <div class="col d-flex">
                                    @include('storefront.product-card', ['product' => $product])
                                </div>
                            @endforeach
                        </div>

                        <nav class="d-flex justify-content-center mt-5" aria-label="Shop pagination">
                            <ul class="pagination mb-0">
                                <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $products->previousPageUrl() ?: '#' }}">Previous</a>
                                </li>
                                <li class="page-item active">
                                    <span class="page-link">{{ $products->currentPage() }} / {{ $products->lastPage() }}</span>
                                </li>
                                <li class="page-item {{ $products->hasMorePages() ? '' : 'disabled' }}">
                                    <a class="page-link" href="{{ $products->nextPageUrl() ?: '#' }}">Next</a>
                                </li>
                            </ul>
                        </nav>
                    @else
                        <div class="text-center border rounded p-5">
                            <h2 class="h4">No products found</h2>
                            <p class="text-secondary">Try another collection, fabric, price range or search term.</p>
                            <a class="btn btn-outline-dark" href="{{ route('shop') }}">Clear filters</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="shopFilters" aria-labelledby="shopFiltersLabel">
        <div class="offcanvas-header">
            <h2 class="offcanvas-title h5" id="shopFiltersLabel">Filter & sort</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            @include('storefront.shop-filters')
        </div>
    </div>
@endsection
