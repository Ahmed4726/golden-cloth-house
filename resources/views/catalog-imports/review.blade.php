@extends('layouts.app')

@section('title', 'Review catalog')

@section('content')
    @php($activeProductCount = $catalogImport->products->where('active', true)->count())
    @php($hasUnpublishedItems = $activeProductCount < $catalogImport->products->count() || ($catalogImport->generatedBrand && ! $catalogImport->generatedBrand->active) || ($catalogImport->generatedSlide && ! $catalogImport->generatedSlide->active))
    <div class="container-fluid py-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
            <div>
                <div class="text-uppercase small fw-bold text-warning-emphasis mb-1">Catalog review</div>
                <h1 class="h2 fw-bold mb-1">{{ $catalogImport->name }}</h1>
                <p class="text-secondary mb-0">
                    {{ $catalogImport->products->count() }} products
                    @if ($catalogImport->status === 'published' || ($catalogImport->products->isNotEmpty() && $activeProductCount === $catalogImport->products->count()))
                        · Published
                    @elseif ($activeProductCount > 0)
                        · {{ $activeProductCount }} published
                    @else
                        · Not visible in the shop until you publish
                    @endif
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-dark" href="{{ route('catalog-imports.index') }}">Back to catalogs</a>
                @if ($catalogImport->status === 'completed' && $hasUnpublishedItems)
                    <form method="POST" action="{{ route('catalog-imports.publish', $catalogImport) }}" onsubmit="return confirm('Publish all products, the generated brand, and homepage slide?');">
                        @csrf
                        <button class="btn btn-dark" type="submit">Publish catalog</button>
                    </form>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($catalogImport->generatedBrand || $catalogImport->generatedSlide)
            <div class="row g-3 mb-4">
                @if ($catalogImport->generatedBrand)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                @if ($catalogImport->generatedBrand->logo_path)
                                    <img class="rounded border object-fit-contain bg-light" src="{{ Storage::url($catalogImport->generatedBrand->logo_path) }}" alt="{{ $catalogImport->generatedBrand->name }} logo" width="88" height="88">
                                @endif
                                <div class="flex-grow-1">
                                    <div class="text-uppercase small text-secondary">Brand draft</div>
                                    <h2 class="h5 fw-bold mb-1">{{ $catalogImport->generatedBrand->name }}</h2>
                                    <span class="badge {{ $catalogImport->generatedBrand->active ? 'text-bg-success' : 'text-bg-warning' }}">{{ $catalogImport->generatedBrand->active ? 'Published' : 'Draft' }}</span>
                                </div>
                                <a class="btn btn-sm btn-outline-dark" href="{{ route('brands.edit', $catalogImport->generatedBrand) }}">Edit</a>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($catalogImport->generatedSlide)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            @if ($catalogImport->generatedSlide->image_path)
                                <img class="card-img-top object-fit-cover" src="{{ $catalogImport->generatedSlide->imageUrl() }}" alt="{{ $catalogImport->generatedSlide->title }}" style="max-height: 180px;">
                            @endif
                            <div class="card-body d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <div class="text-uppercase small text-secondary">Homepage slide draft</div>
                                    <h2 class="h5 fw-bold mb-1">{{ $catalogImport->generatedSlide->title }}</h2>
                                    <span class="badge {{ $catalogImport->generatedSlide->active ? 'text-bg-success' : 'text-bg-warning' }}">{{ $catalogImport->generatedSlide->active ? 'Published' : 'Draft' }}</span>
                                </div>
                                <a class="btn btn-sm btn-outline-dark" href="{{ route('slides.edit', $catalogImport->generatedSlide) }}">Edit</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
            @forelse ($catalogImport->products as $product)
                <div class="col">
                    <article class="card h-100 border-0 shadow-sm">
                        @if ($product->images())
                            <div class="row g-1 p-2">
                                @foreach (array_slice($product->images(), 0, 3) as $image)
                                    <div class="{{ $loop->first && count($product->images()) === 1 ? 'col-12' : 'col-4' }}">
                                        <img class="w-100 rounded object-fit-contain bg-light" src="{{ $product->imageUrl($image) }}" alt="{{ $product->name }}" style="height: 180px;">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <h2 class="h5 fw-bold">{{ $product->name }}</h2>
                                <span class="badge {{ $product->active ? 'text-bg-success' : 'text-bg-warning' }}">{{ $product->active ? 'Published' : 'Draft' }}</span>
                            </div>
                            <p class="text-secondary small mb-2">{{ $product->brand?->name }}{{ $product->categoryRelation ? ' · '.$product->categoryRelation->name : '' }}</p>
                            <p class="mb-1"><strong>PKR {{ number_format((float) $product->price, 0) }}</strong>
                                @if ($product->compare_at_price)
                                    <span class="text-secondary text-decoration-line-through ms-1">PKR {{ number_format((float) $product->compare_at_price, 0) }}</span>
                                @endif
                            </p>
                            <p class="text-secondary small mb-0">{{ $product->design_code }}{{ $product->fabric ? ' · '.$product->fabric : '' }}</p>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <a class="btn btn-sm btn-outline-dark" href="{{ route('products.edit', $product) }}">Review / edit product</a>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border">No products have been generated from this catalog yet.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
