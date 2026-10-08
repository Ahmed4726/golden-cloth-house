<div class="row g-3">
    <div class="col-12"><label class="form-label fw-semibold" for="category-name">Name</label><input class="form-control" id="category-name" name="name" required value="{{ old('name', $category->name ?? '') }}"></div>
    <div class="col-12"><label class="form-label fw-semibold" for="category-slug">Slug</label><input class="form-control" id="category-slug" name="slug" value="{{ old('slug', $category->slug ?? '') }}" placeholder="auto-generated-if-empty"></div>
    <div class="col-12"><label class="form-label fw-semibold" for="category-description">Description</label><textarea class="form-control" id="category-description" name="description" rows="5">{{ old('description', $category->description ?? '') }}</textarea></div>
    <div class="col-md-6"><label class="form-label fw-semibold" for="category-sort">Sort order</label><input class="form-control" id="category-sort" type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}"></div>
    <div class="col-md-6 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" id="category-active" type="checkbox" name="active" value="1" {{ old('active', isset($category) ? $category->active : true) ? 'checked' : '' }}><label class="form-check-label" for="category-active">Active</label></div></div>
</div>
