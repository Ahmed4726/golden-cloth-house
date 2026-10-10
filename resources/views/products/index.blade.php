@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div><h1 class="h2 fw-bold mb-1">Product Catalogue</h1><div class="text-secondary">Products power your website and social posts.</div></div>
        <a class="btn btn-dark" href="{{ route('products.create') }}"><i class="fa-solid fa-plus me-1"></i>Add Product</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Product</th><th>SKU</th><th>Price</th><th>Stock</th><th>Images</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @php
                                        $image = $product->images()[0] ?? null;
                                        $imageUrl = $image
                                            ? $product->imageUrl($image)
                                            : asset('images/product-placeholder.svg');
                                    @endphp
                                    <img
                                        class="rounded object-fit-contain bg-light flex-shrink-0"
                                        src="{{ $imageUrl }}"
                                        width="56"
                                        height="72"
                                        alt="{{ $product->name }}"
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
                                    >
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                        <div class="text-secondary small">{{ $product->category ?: 'Uncategorised' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->sku ?: '—' }}</td>
                            <td>{{ $product->price ? number_format((float) $product->price, 0).' '.$product->currency : '—' }}</td>
                            <td>{{ $product->stock_quantity }}</td>
                            <td>{{ count($product->images()) }}</td>
                            <td><span class="badge {{ $product->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $product->active ? 'Active' : 'Hidden' }}</span></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-sm btn-outline-dark" href="{{ route('products.edit', $product) }}">Edit</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-secondary">No products yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $products->links() }}</div>
    </div>
@endsection
