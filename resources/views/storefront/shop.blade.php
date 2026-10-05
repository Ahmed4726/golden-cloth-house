@extends('storefront.layout')

@php
    $title = 'Shop | '.config('store.name');
    $description = 'Browse the latest clothing collection from '.config('store.name').'. Cash on Delivery available across Pakistan.';
@endphp

@section('content')
    <section class="page-head">
        <span class="eyebrow">COLLECTION</span>
        <h1 style="font-size:clamp(42px,6vw,64px);margin:10px 0;">Shop</h1>
        <p>Explore our latest styles. Select a product to view the complete gallery and order with Cash on Delivery.</p>
    </section>

    <form class="filters" method="get" action="{{ route('shop') }}">
        <input
            class="input"
            type="search"
            name="q"
            value="{{ request('q') }}"
            placeholder="Search products, categories or SKU..."
            aria-label="Search products"
        >

        <select class="select" name="category" aria-label="Filter by category">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category }}" @selected(request('category') === $category)>
                    {{ $category }}
                </option>
            @endforeach
        </select>

        <button class="btn" type="submit">Search</button>
    </form>

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
            <p class="muted">Try another search or category.</p>
            <a class="btn secondary" href="{{ route('shop') }}">Clear filters</a>
        </div>
    @endif
@endsection
