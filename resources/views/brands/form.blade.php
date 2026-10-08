<div class="row g-3">
    <div class="col-12"><label class="form-label fw-semibold" for="brand-name">Name</label><input class="form-control" id="brand-name" name="name" required value="{{ old('name', $brand->name ?? '') }}"></div>
    <div class="col-12"><label class="form-label fw-semibold" for="brand-slug">Slug</label><input class="form-control" id="brand-slug" name="slug" value="{{ old('slug', $brand->slug ?? '') }}" placeholder="auto-generated-if-empty"></div>
    <div class="col-12"><label class="form-label fw-semibold" for="brand-description">Description</label><textarea class="form-control" id="brand-description" name="description" rows="5">{{ old('description', $brand->description ?? '') }}</textarea></div>
    <div class="col-md-6"><label class="form-label fw-semibold" for="brand-sort">Sort order</label><input class="form-control" id="brand-sort" type="number" min="0" name="sort_order" value="{{ old('sort_order', $brand->sort_order ?? 0) }}"></div>
    <div class="col-md-6 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" id="brand-active" type="checkbox" name="active" value="1" {{ old('active', isset($brand) ? $brand->active : true) ? 'checked' : '' }}><label class="form-check-label" for="brand-active">Active</label></div></div>
    <div class="col-12"><label class="form-label fw-semibold" for="brand-logo">Logo</label><input class="form-control" id="brand-logo" type="file" name="logo" accept="image/jpeg,image/png,image/webp"></div>
    @if (!empty($brand?->logo_path))
        <div class="col-12"><div class="ratio ratio-1x1 bg-light rounded overflow-hidden w-25"><img class="w-100 h-100 object-fit-contain" src="{{ asset('storage/'.$brand->logo_path) }}" alt="{{ $brand->name }}"></div></div>
    @endif
</div>
