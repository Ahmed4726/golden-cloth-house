@extends('storefront.layout')

@php
    $title = 'Shop | '.config('store.name');
    $description = 'Shop women and men unstitched wear from '.config('store.name').'. Filter by category, fabric, price, availability and newness.';
@endphp

@section('content')
    <section class="page-head">
        <div class="container">
            <div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / Shop</div>
            <span class="section-kicker">The collection</span>
            <h1>Shop Unstitched Wear</h1>
            <p>Women and men unstitched clothing, with printed, embroidered and seasonal collections available for delivery across Pakistan.</p>
        </div>
    </section>

    <section class="section" style="padding-top:15px">
        <div class="container">
            <button class="btn outline mobile-filter-button" type="button" data-filter-toggle>Filter & sort</button>

            <div class="shop-layout">
                <aside class="filters collapsed" data-filters>
                    <form class="filter-form" method="get" action="{{ route('shop') }}">
                        <div class="filter-group">
                            <div class="filter-title">Search</div>
                            <input class="input" type="search" name="q" value="{{ $filter['q'] }}" placeholder="Search products...">
                        </div>

                        <div class="filter-group">
                            <div class="filter-title">Collection</div>
                            <div class="filter-links">
                                <label><input type="radio" name="category" value="" @checked($filter['category'] === '')> All products</label>
                                @foreach ($categories as $category)
                                    <label>
                                        <input type="radio" name="category" value="{{ $category->slug }}" @checked($filter['category'] === $category->slug || $filter['category'] == $category->id)>
                                        {{ $category->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="filter-group">
                            <div class="filter-title">Brand</div>
                            <select class="select" name="brand">
                                <option value="">All brands</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected($filter['brand'] == $brand->id)>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <div class="filter-title">Price</div>
                            <div class="price-fields">
                                <input class="input" type="number" name="min_price" value="{{ $filter['min_price'] }}" min="0" placeholder="Min">
                                <input class="input" type="number" name="max_price" value="{{ $filter['max_price'] }}" min="0" placeholder="Max">
                            </div>
                        </div>

                        <div class="filter-group">
                            <div class="filter-title">Availability</div>
                            <label class="filter-links"><span><input type="checkbox" name="in_stock" value="1" @checked($filter['in_stock'])> In stock only</span></label>
                            <label class="filter-links" style="margin-top:8px"><span><input type="checkbox" name="sale" value="1" @checked($filter['sale'])> Sale items</span></label>
                        </div>

                        <div class="filter-group">
                            <div class="filter-title">Sort</div>
                            <select class="select" name="sort">
                                <option value="featured" @selected($filter['sort'] === 'featured')>Featured</option>
                                <option value="newest" @selected($filter['sort'] === 'newest')>Newest</option>
                                <option value="price_low" @selected($filter['sort'] === 'price_low')>Price: Low to High</option>
                                <option value="price_high" @selected($filter['sort'] === 'price_high')>Price: High to Low</option>
                                <option value="name_az" @selected($filter['sort'] === 'name_az')>Name: A to Z</option>
                            </select>
                        </div>

                        <button class="btn full" type="submit">Apply filters</button>
                        <a class="btn outline full" href="{{ route('shop') }}" style="margin-top:8px">Clear all</a>
                    </form>
                </aside>

                <div>
                    <div class="shop-top">
                        <div class="count">{{ $products->total() }} products</div>
                        <div class="count">
                            Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}
                            @if ($filter['sale']) · Sale @endif
                        </div>
                    </div>

                    @if ($products->isNotEmpty())
                        <div class="product-grid">
                            @foreach ($products as $product)
                                @include('storefront.product-card', ['product' => $product])
                            @endforeach
                        </div>

                        <div class="pagination">
                            @if ($products->onFirstPage())
                                <span>Previous</span>
                            @else
                                <a href="{{ $products->previousPageUrl() }}">Previous</a>
                            @endif

                            <span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>

                            @if ($products->hasMorePages())
                                <a href="{{ $products->nextPageUrl() }}">Next</a>
                            @else
                                <span>Next</span>
                            @endif
                        </div>
                    @else
                        <div class="empty">
                            <h3>No products found</h3>
                            <p>Try another collection, fabric, price range or search term.</p>
                            <a class="btn outline" href="{{ route('shop') }}">Clear filters</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const button = document.querySelector('[data-filter-toggle]');
            const filters = document.querySelector('[data-filters]');

            if (!button || !filters) return;

            button.addEventListener('click', () => {
                filters.classList.toggle('collapsed');
            });
        });
    </script>
@endpush
