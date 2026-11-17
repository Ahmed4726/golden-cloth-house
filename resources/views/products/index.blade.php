@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div><h1 class="h2 fw-bold mb-1">Product Catalogue</h1><div class="text-secondary">Products power your website and social posts.</div></div>
        <a class="btn btn-dark" href="{{ route('products.create') }}"><i class="fa-solid fa-plus me-1"></i>Add Product</a>
    </div>

    <form class="card border-0 shadow-sm mb-4" method="GET" action="{{ route('products.index') }}">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-lg-4">
                    <label class="form-label" for="product-search">Search products</label>
                    <input class="form-control" id="product-search" name="q" value="{{ request('q') }}" placeholder="Name or SKU">
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label" for="product-category">Category</label>
                    <select class="form-select" id="product-category" name="category_id">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label" for="product-brand">Brand</label>
                    <select class="form-select" id="product-brand" name="brand_id">
                        <option value="">All brands</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected((string) request('brand_id') === (string) $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label" for="product-status">Status</label>
                    <select class="form-select" id="product-status" name="status">
                        <option value="">All statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="hidden" @selected(request('status') === 'hidden')>Hidden</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-2 d-flex gap-2">
                    <button class="btn btn-dark flex-grow-1" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('products.index') }}">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <form
        id="bulk-sale-form"
        class="card border-0 shadow-sm mb-4"
        method="POST"
        action="{{ route('products.bulk-sale.update', request()->only(['q', 'category_id', 'brand_id', 'status', 'page'])) }}"
    >
        @csrf
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg">
                    <h2 class="h5 fw-bold mb-1">Bulk sale pricing</h2>
                    <p class="text-secondary small mb-0">Select products on this page. The discount uses each product’s original price; removing a sale restores that price.</p>
                    <div class="small mt-2" aria-live="polite">
                        <span id="selected-products-count">0</span> products selected
                    </div>
                </div>
                <div class="col-12 col-sm-5 col-lg-3">
                    <label class="form-label" for="bulk-sale-discount">Discount percentage</label>
                    <div class="input-group">
                        <input class="form-control" id="bulk-sale-discount" type="number" name="discount_percent" min="0.01" max="100" step="0.01" value="{{ old('discount_percent') }}" placeholder="e.g. 20">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-12 col-sm-7 col-lg-auto d-flex flex-wrap gap-2">
                    <button class="btn btn-dark" type="submit" name="action" value="apply">Apply discount</button>
                    <button class="btn btn-outline-secondary" type="submit" name="action" value="remove">Remove sale</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>
                            <input class="form-check-input" id="select-page-products" type="checkbox" aria-label="Select all products on this page" @disabled($products->count() === 0)>
                        </th>
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
                                <input
                                    class="form-check-input product-selection"
                                    id="select-product-{{ $product->id }}"
                                    type="checkbox"
                                    name="product_ids[]"
                                    value="{{ $product->id }}"
                                    form="bulk-sale-form"
                                    aria-label="Select {{ $product->name }}"
                                    @checked(is_array(old('product_ids')) && in_array((string) $product->id, old('product_ids'), true))
                                >
                            </td>
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
                            <td>
                                {{ number_format((float) $product->price, 0).' '.$product->currency }}
                                @if ($product->compare_at_price && $product->compare_at_price > $product->price)
                                    <span class="text-secondary text-decoration-line-through small d-block">{{ number_format((float) $product->compare_at_price, 0).' '.$product->currency }}</span>
                                @endif
                            </td>
                            <td>{{ $product->stock_quantity }}</td>
                            <td>{{ count($product->images()) }}</td>
                            <td><span class="badge {{ $product->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $product->active ? 'Active' : 'Hidden' }}</span></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-sm btn-outline-success" href="{{ route('products.sales.create', $product) }}">Record sale</a>
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
                        <tr><td colspan="8" class="text-secondary">No products yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <span class="text-secondary small">
                @if ($products->total())
                    Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} products
                @else
                    No matching products
                @endif
            </span>
            {{ $products->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const selectPage = document.querySelector('#select-page-products');
                const productCheckboxes = Array.from(document.querySelectorAll('.product-selection'));
                const selectedCount = document.querySelector('#selected-products-count');

                const updateSelectedCount = () => {
                    selectedCount.textContent = productCheckboxes.filter((checkbox) => checkbox.checked).length;
                };

                selectPage?.addEventListener('change', () => {
                    productCheckboxes.forEach((checkbox) => {
                        checkbox.checked = selectPage.checked;
                    });
                    updateSelectedCount();
                });

                productCheckboxes.forEach((checkbox) => {
                    checkbox.addEventListener('change', () => {
                        if (!checkbox.checked) {
                            selectPage.checked = false;
                        } else {
                            selectPage.checked = productCheckboxes.every((productCheckbox) => productCheckbox.checked);
                        }

                        updateSelectedCount();
                    });
                });

                if (selectPage && productCheckboxes.length > 0) {
                    selectPage.checked = productCheckboxes.every((checkbox) => checkbox.checked);
                }

                updateSelectedCount();
            });
        </script>
    @endpush
@endsection
