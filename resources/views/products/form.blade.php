<div class="field">
    <label>Name</label>
    <input name="name" required value="{{ old('name', $product->name ?? '') }}">
</div>

<div class="row">
    <div class="field" style="flex:1"><label>Slug</label><input name="slug" value="{{ old('slug', $product->slug ?? '') }}"></div>
    <div class="field" style="flex:1"><label>SKU</label><input name="sku" value="{{ old('sku', $product->sku ?? '') }}"></div>
</div>

<div class="row">
    <div class="field" style="flex:1"><label>Retail / compare-at price</label><input type="number" step="0.01" min="0" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price ?? '') }}"></div>
    <div class="field" style="flex:1"><label>Sale price</label><input type="number" step="0.01" min="0" name="price" required value="{{ old('price', $product->price ?? '') }}"></div>
    <div class="field" style="width:130px"><label>Currency</label><input name="currency" maxlength="3" value="{{ old('currency', $product->currency ?? config('store.currency')) }}"></div>
</div>

<div class="row">
    <div class="field" style="flex:1"><label>Discount %</label><input type="number" step="0.1" min="0" max="100" name="discount_percent" value="{{ old('discount_percent', $product->discount_percent ?? 0) }}"></div>
    <div class="field" style="flex:1"><label>Stock quantity</label><input type="number" min="0" name="stock_quantity" required value="{{ old('stock_quantity', $product->stock_quantity ?? 3) }}"></div>
</div>

<div class="row">
    <div class="field" style="flex:1">
        <label>Category</label>
        <select name="category_id">
            <option value="">No category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="field" style="flex:1">
        <label>Brand</label>
        <select name="brand_id">
            <option value="">No brand</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="field" style="flex:1"><label>Gender</label><select name="gender"><option value="">Select</option><option value="women" @selected(old('gender', $product->gender ?? '') === 'women')>Women</option><option value="men" @selected(old('gender', $product->gender ?? '') === 'men')>Men</option></select></div>
    <div class="field" style="flex:1"><label>Wear type</label><input name="wear_type" value="{{ old('wear_type', $product->wear_type ?? 'unstitched') }}"></div>
    <div class="field" style="flex:1"><label>Pieces</label><input type="number" min="1" max="10" name="piece_count" value="{{ old('piece_count', $product->piece_count ?? '') }}"></div>
</div>

<div class="row">
    <div class="field" style="flex:1"><label>Fabric</label><input name="fabric" value="{{ old('fabric', $product->fabric ?? '') }}" placeholder="Khaddar, Lawn, Slub…"></div>
    <div class="field" style="flex:1"><label>Season</label><input name="season" value="{{ old('season', $product->season ?? '') }}"></div>
    <div class="field" style="flex:1"><label>Design code</label><input name="design_code" value="{{ old('design_code', $product->design_code ?? '') }}"></div>
</div>

<div class="field"><label>Description</label><textarea name="description">{{ old('description', $product->description ?? '') }}</textarea></div>

<div class="field">
    <label>Upload product images</label>
    <input id="product-images" type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp">
    <small class="muted">Up to 20 images, 10 MB each. Selected files are previewed before saving.</small>
    <div id="new-image-preview" class="image-manager-grid" aria-live="polite"></div>
</div>

@if (isset($product) && $product->media_paths)
    <div class="field">
        <label>Uploaded images</label>
        <div class="image-manager-grid">
            @foreach ($product->media_paths as $path)
                <div class="managed-image">
                    <img src="{{ $product->imageUrl($path) }}" alt="{{ $product->name }}" loading="lazy">
                    <div class="managed-image-footer">
                        <span class="muted">{{ basename($path) }}</span>
                        <form method="POST" action="{{ route('products.images.remove', $product) }}" onsubmit="return confirm('Remove this image?');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="type" value="media">
                            <input type="hidden" name="value" value="{{ $path }}">
                            <button class="image-delete" type="submit">Remove</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="field">
    <label>External image URLs (optional, one per line)</label>
    <textarea name="image_urls">{{ old('image_urls', isset($product) ? implode("\n", $product->image_urls ?? []) : '') }}</textarea>
    @if (isset($product) && $product->image_urls)
        <div class="image-manager-grid">
            @foreach ($product->image_urls as $url)
                <div class="managed-image">
                    <img src="{{ $url }}" alt="{{ $product->name }}" loading="lazy" onerror="this.classList.add('image-error')">
                    <div class="managed-image-footer">
                        <span class="muted">External image</span>
                        <form method="POST" action="{{ route('products.images.remove', $product) }}" onsubmit="return confirm('Remove this image URL?');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="type" value="url">
                            <input type="hidden" name="value" value="{{ $url }}">
                            <button class="image-delete" type="submit">Remove</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="row" style="margin-bottom:16px;">
    <label class="check"><input type="checkbox" name="active" value="1" {{ old('active', isset($product) ? $product->active : true) ? 'checked' : '' }}> Active and visible on the shop</label>
    <label class="check"><input type="checkbox" name="featured" value="1" {{ old('featured', isset($product) ? $product->featured : false) ? 'checked' : '' }}> Featured on homepage</label>
</div>

<div class="flash"><strong>Image storage:</strong> uploaded files are stored on Laravel's public disk. Run <code>php artisan storage:link</code> after deployment.</div>

<style>
    .image-manager-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:12px; margin-top:12px; }
    .managed-image { border:1px solid #e7e9ef; border-radius:12px; overflow:hidden; background:#fff; }
    .managed-image img { display:block; width:100%; aspect-ratio:4/5; object-fit:cover; background:#f8fafc; }
    .managed-image img.image-error { opacity:.25; }
    .managed-image-footer { padding:8px; display:flex; justify-content:space-between; align-items:center; gap:8px; }
    .managed-image-footer .muted { font-size:11px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .image-delete { border:0; background:#fee2e2; color:#b91c1c; border-radius:7px; padding:5px 8px; cursor:pointer; font-size:12px; font-weight:700; }
    #new-image-preview img { width:100%; aspect-ratio:4/5; object-fit:cover; border-radius:10px; border:1px solid #e7e9ef; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('product-images');
        const preview = document.getElementById('new-image-preview');
        if (!input || !preview) return;

        input.addEventListener('change', () => {
            preview.innerHTML = '';
            [...input.files].forEach(file => {
                const reader = new FileReader();
                reader.onload = event => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'managed-image';
                    wrapper.innerHTML = `<img src="${event.target.result}" alt="New product image"><div class="managed-image-footer"><span class="muted">${file.name}</span><span class="muted">Ready</span></div>`;
                    preview.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        });
    });
</script>
