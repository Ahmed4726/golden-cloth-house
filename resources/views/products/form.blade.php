<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold" for="product-name">Name</label>
        <input class="form-control" id="product-name" name="name" required value="{{ old('name', $product->name ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold" for="product-slug">Slug</label>
        <input class="form-control" id="product-slug" name="slug" value="{{ old('slug', $product->slug ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="product-sku">SKU</label>
        <input class="form-control" id="product-sku" name="sku" value="{{ old('sku', $product->sku ?? '') }}">
    </div>

    <div class="col-md-5">
        <label class="form-label fw-semibold" for="compare-price">Retail / compare-at price</label>
        <input class="form-control" id="compare-price" type="number" step="0.01" min="0" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price ?? '') }}">
    </div>
    <div class="col-md-5">
        <label class="form-label fw-semibold" for="sale-price">Sale price</label>
        <input class="form-control" id="sale-price" type="number" step="0.01" min="0" name="price" required value="{{ old('price', $product->price ?? '') }}">
    </div>
    <div class="col-md-2">
        <label class="form-label fw-semibold" for="currency">Currency</label>
        <input class="form-control" id="currency" name="currency" maxlength="3" value="{{ old('currency', $product->currency ?? config('store.currency')) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold" for="discount">Discount %</label>
        <input class="form-control" id="discount" type="number" step="0.1" min="0" max="100" name="discount_percent" value="{{ old('discount_percent', $product->discount_percent ?? 0) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="stock">Stock quantity</label>
        <input class="form-control" id="stock" type="number" min="0" name="stock_quantity" required value="{{ old('stock_quantity', $product->stock_quantity ?? 3) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold" for="category">Category</label>
        <select class="form-select" id="category" name="category_id">
            <option value="">No category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="brand">Brand</label>
        <select class="form-select" id="brand" name="brand_id">
            <option value="">No brand</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold" for="gender">Gender</label>
        <select class="form-select" id="gender" name="gender">
            <option value="">Select</option>
            <option value="women" @selected(old('gender', $product->gender ?? '') === 'women')>Women</option>
            <option value="men" @selected(old('gender', $product->gender ?? '') === 'men')>Men</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="wear-type">Wear type</label>
        <input class="form-control" id="wear-type" name="wear_type" value="{{ old('wear_type', $product->wear_type ?? 'unstitched') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="piece-count">Pieces</label>
        <input class="form-control" id="piece-count" type="number" min="1" max="10" name="piece_count" value="{{ old('piece_count', $product->piece_count ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold" for="fabric">Fabric</label>
        <input class="form-control" id="fabric" name="fabric" value="{{ old('fabric', $product->fabric ?? '') }}" placeholder="Khaddar, Lawn, Slub…">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="season">Season</label>
        <input class="form-control" id="season" name="season" value="{{ old('season', $product->season ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="design-code">Design code</label>
        <input class="form-control" id="design-code" name="design_code" value="{{ old('design_code', $product->design_code ?? '') }}">
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold" for="description">Description</label>
        <textarea class="form-control" id="description" name="description" rows="6">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold" for="product-images">Upload product images</label>
        <input class="form-control" id="product-images" type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp">
        <div class="form-text">Up to 20 images, 10 MB each. Selected files are previewed before saving.</div>
        <div id="new-image-preview" class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-2 mt-1" aria-live="polite"></div>
    </div>

    @if (isset($product) && $product->media_paths)
        <div class="col-12">
            <label class="form-label fw-semibold">Uploaded images</label>
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-2">
                @foreach ($product->media_paths as $path)
                    <div class="col">
                        <div class="card h-100">
                            <img class="card-img-top object-fit-contain bg-light" src="{{ $product->imageUrl($path) }}" alt="{{ $product->name }}" loading="lazy">
                            <div class="card-body p-2">
                                <div class="small text-secondary text-truncate mb-2">{{ basename($path) }}</div>
                                <form method="POST" action="{{ route('products.images.remove', $product) }}" onsubmit="return confirm('Remove this image?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="type" value="media">
                                    <input type="hidden" name="value" value="{{ $path }}">
                                    <button class="btn btn-sm btn-outline-danger w-100" type="submit">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="col-12">
        <label class="form-label fw-semibold" for="image-urls">External image URLs (optional, one per line)</label>
        <textarea class="form-control" id="image-urls" name="image_urls" rows="4">{{ old('image_urls', isset($product) ? implode("\n", $product->image_urls ?? []) : '') }}</textarea>
    </div>

    @if (isset($product) && $product->image_urls)
        <div class="col-12">
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-2">
                @foreach ($product->image_urls as $url)
                    <div class="col">
                        <div class="card h-100">
                            <img class="card-img-top object-fit-contain bg-light" src="{{ $url }}" alt="{{ $product->name }}" loading="lazy" onerror="this.classList.add('opacity-25')">
                            <div class="card-body p-2">
                                <div class="small text-secondary mb-2">External image</div>
                                <form method="POST" action="{{ route('products.images.remove', $product) }}" onsubmit="return confirm('Remove this image URL?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="type" value="url">
                                    <input type="hidden" name="value" value="{{ $url }}">
                                    <button class="btn btn-sm btn-outline-danger w-100" type="submit">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="col-12">
        <div class="d-flex flex-wrap gap-4">
            <div class="form-check">
                <input class="form-check-input" id="active" type="checkbox" name="active" value="1" {{ old('active', isset($product) ? $product->active : true) ? 'checked' : '' }}>
                <label class="form-check-label" for="active">Active and visible on the shop</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" id="featured" type="checkbox" name="featured" value="1" {{ old('featured', isset($product) ? $product->featured : false) ? 'checked' : '' }}>
                <label class="form-check-label" for="featured">Featured on homepage</label>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="alert alert-light border mb-0"><strong>Image storage:</strong> uploaded files are stored on Laravel's public disk. Run <code>php artisan storage:link</code> after deployment.</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('product-images');
        const preview = document.getElementById('new-image-preview');
        if (!input || !preview) return;

        input.addEventListener('change', () => {
            preview.innerHTML = '';
            [...input.files].forEach((file) => {
                const reader = new FileReader();
                reader.onload = (event) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'col';
                    wrapper.innerHTML = `
                        <div class="card h-100">
                            <img class="card-img-top object-fit-contain bg-light" src="${event.target.result}" alt="New product image">
                            <div class="card-body p-2"><div class="small text-secondary text-truncate">${file.name}</div></div>
                        </div>
                    `;
                    preview.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        });
    });
</script>
