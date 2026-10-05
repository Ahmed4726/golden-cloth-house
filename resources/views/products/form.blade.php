<div class="field">
    <label>Name</label>
    <input name="name" required value="{{ old('name', $product->name ?? '') }}">
</div>

<div class="row">
    <div class="field" style="flex:1">
        <label>Slug</label>
        <input name="slug" value="{{ old('slug', $product->slug ?? '') }}" placeholder="auto-generated-if-empty">
    </div>

    <div class="field" style="flex:1">
        <label>SKU</label>
        <input name="sku" value="{{ old('sku', $product->sku ?? '') }}">
    </div>
</div>

<div class="row">
    <div class="field" style="flex:1">
        <label>Price</label>
        <input type="number" step="0.01" min="0" name="price" required value="{{ old('price', $product->price ?? '') }}">
    </div>

    <div class="field" style="width:130px">
        <label>Currency</label>
        <input name="currency" maxlength="3" value="{{ old('currency', $product->currency ?? config('store.currency')) }}">
    </div>

    <div class="field" style="flex:1">
        <label>Compare-at price</label>
        <input type="number" step="0.01" min="0" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price ?? '') }}">
    </div>
</div>

<div class="row">
    <div class="field" style="flex:1">
        <label>Stock quantity</label>
        <input type="number" min="0" name="stock_quantity" required value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}">
    </div>

    <div class="field" style="flex:1">
        <label>Category</label>
        <input name="category" value="{{ old('category', $product->category ?? '') }}">
    </div>

    <div class="field" style="width:130px">
        <label>Sort order</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}">
    </div>
</div>

<div class="field">
    <label>Description</label>
    <textarea name="description">{{ old('description', $product->description ?? '') }}</textarea>
</div>

<div class="field">
    <label>Upload product images</label>
    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp">
    <small class="muted">
        Up to 20 images, 10 MB each. JPG, PNG and WebP are supported.
        Uploaded files are stored in the public disk under <code>products/</code>.
    </small>
</div>

@if (isset($product) && $product->media_paths)
    <div class="field">
        <label>Uploaded images</label>
        <div class="row" style="flex-wrap:wrap;align-items:flex-start;">
            @foreach ($product->media_paths as $path)
                <div>
                    <img
                        src="{{ asset('storage/'.$path) }}"
                        alt="{{ $product->name }}"
                        style="width:90px;height:110px;object-fit:cover;border-radius:8px;display:block;"
                    >
                    <small class="muted">{{ $path }}</small>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="field">
    <label>External image URLs (optional, one per line)</label>
    <textarea name="image_urls" placeholder="https://example.com/image.jpg">{{ old('image_urls', isset($product) ? implode("\n", $product->image_urls ?? []) : '') }}</textarea>
</div>

<div class="row" style="margin-bottom:16px;">
    <label class="check">
        <input type="checkbox" name="active" value="1" {{ old('active', isset($product) ? $product->active : true) ? 'checked' : '' }}>
        Active and visible on the shop
    </label>

    <label class="check">
        <input type="checkbox" name="featured" value="1" {{ old('featured', isset($product) ? $product->featured : false) ? 'checked' : '' }}>
        Featured on homepage
    </label>
</div>

<div class="flash">
    <strong>Image storage:</strong>
    make sure the deployment has run <code>php artisan storage:link</code> so uploaded images are publicly visible.
</div>
