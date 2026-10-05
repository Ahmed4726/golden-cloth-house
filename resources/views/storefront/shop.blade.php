@extends('storefront.layout')

@php
    $title = 'Shop | '.config('store.name');
    $description = 'Browse the latest clothing collection from '.config('store.name').'. Cash on Delivery available across Pakistan.';
@endphp

@section('content')
<section class="page-head shop-page-head">
    <span class="eyebrow">COLLECTION</span>
    <h1>Shop</h1>
    <p>Explore our latest styles and narrow your selection by category, brand, price, availability, or sort order.</p>
</section>

<form class="shop-filters" method="get" action="{{ route('shop') }}">
    <div class="filter-search">
        <label class="form-label" for="shop-search">Search</label>
        <input id="shop-search" class="input" type="search" name="q" value="{{ $filter['q'] }}" placeholder="Search products, categories, brands or SKU...">
    </div>

    <div>
        <label class="form-label" for="shop-category">Category</label>
        <select id="shop-category" class="select" name="category">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected($filter['category'] == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="shop-brand">Brand</label>
        <select id="shop-brand" class="select" name="brand">
            <option value="">All brands</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected($filter['brand'] == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="shop-sort">Sort by</label>
        <select id="shop-sort" class="select" name="sort">
            <option value="featured" @selected($filter['sort'] === 'featured')>Featured</option>
            <option value="newest" @selected($filter['sort'] === 'newest')>Newest</option>
            <option value="price_low" @selected($filter['sort'] === 'price_low')>Price: Low to High</option>
            <option value="price_high" @selected($filter['sort'] === 'price_high')>Price: High to Low</option>
            <option value="name_az" @selected($filter['sort'] === 'name_az')>Name: A to Z</option>
        </select>
    </div>

    <div class="price-filter">
        <span class="form-label">Price range</span>
        <div class="price-fields">
            <input class="input" type="number" name="min_price" value="{{ $filter['min_price'] }}" min="0" step="1" placeholder="Min">
            <input class="input" type="number" name="max_price" value="{{ $filter['max_price'] }}" min="0" step="1" placeholder="Max">
        </div>
    </div>

    <label class="stock-filter"><input type="checkbox" name="in_stock" value="1" @checked($filter['in_stock'])><span>In stock only</span></label>

    <div class="filter-actions"><button class="btn" type="submit">Apply filters</button><a class="btn secondary" href="{{ route('shop') }}">Clear</a></div>
</form>

<div class="shop-results-bar">
    <span class="muted">Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</span>
    @if ($filter['q'] || $filter['category'] || $filter['brand'] || $filter['min_price'] !== '' || $filter['max_price'] !== '' || $filter['in_stock'])
        <span class="filter-active">Filters applied</span>
    @endif
</div>

@if ($products->isNotEmpty())
    <div class="product-grid">
        @foreach ($products as $product)
            @include('storefront.product-card', ['product' => $product])
        @endforeach
    </div>
    <div class="pagination">
        @if ($products->onFirstPage()) <span>Previous</span> @else <a href="{{ $products->previousPageUrl() }}">Previous</a> @endif
        <span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>
        @if ($products->hasMorePages()) <a href="{{ $products->nextPageUrl() }}">Next</a> @else <span>Next</span> @endif
    </div>
@else
    <div class="empty">
        <h3>No products found</h3>
        <p class="muted">Try changing your search, category, brand, price range, or availability filter.</p>
        <a class="btn secondary" href="{{ route('shop') }}">Clear all filters</a>
    </div>
@endif
@endsection
