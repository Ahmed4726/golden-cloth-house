@extends('storefront.layout')

@php
    $title = 'Shop | '.config('store.name');
    $description = 'Shop women and men unstitched wear from '.config('store.name').'. Filter by category, fabric, price, availability and newness.';
@endphp

@section('content')
    <section class="py-5 text-center">
        <div class="container">
            <div class="small text-uppercase text-secondary mb-2">
                <a class="text-dark text-decoration-none" href="{{ route('home') }}">Home</a> / Shop
            </div>
            <div class="small text-uppercase text-secondary fw-bold">The collection</div>
            <h1 class="display-5 fw-normal">Shop Unstitched Wear</h1>
            <p class="text-secondary mx-auto col-lg-7 mb-0">
                Women and men unstitched clothing, with printed, embroidered and seasonal collections available for delivery across Pakistan.
            </p>
        </div>
    </section>

    <section class="pb-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
                <button class="btn btn-outline-dark d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#shopFilters" aria-controls="shopFilters">
                    Filter & sort
                </button>
                <div class="text-secondary">
                    {{ $products->total() }} products · Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}
                    @if ($filter['sale']) · Sale @endif
                </div>
            </div>

            <div class="row g-4">
                <aside class="col-lg-3 d-none d-lg-block">
                    @include('storefront.shop-filters')
                </aside>

                <div class="col-lg-9">
                    @if ($products->isNotEmpty())
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
