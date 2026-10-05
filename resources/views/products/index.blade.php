@extends('layouts.app')

@section('content')
    <div class="top">
        <div>
            <h1>Product Catalogue</h1>
            <div class="muted">Products power your website and social posts.</div>
        </div>

        <a class="btn" href="{{ route('products.create') }}">Add Product</a>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Images</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            <div class="muted">{{ $product->category ?: 'Uncategorised' }}</div>
                        </td>
                        <td>{{ $product->sku ?: '—' }}</td>
                        <td>
                            @if ($product->price)
                                {{ number_format((float) $product->price, 0) }} {{ $product->currency }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td>{{ count($product->images()) }}</td>
                        <td>
                            @if ($product->active)
                                <span class="badge ok">Active</span>
                            @else
                                <span class="badge">Hidden</span>
                            @endif
                        </td>
                        <td>
                            <div class="row">
                                <a class="btn secondary" href="{{ route('products.edit', $product) }}">Edit</a>

                                <form method="POST" action="{{ route('products.destroy', $product) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No products yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $products->links() }}
    </div>
@endsection
