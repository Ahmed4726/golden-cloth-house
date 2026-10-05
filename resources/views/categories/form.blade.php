<div class="field"><label>Name</label><input name="name" required value="{{ old('name', $category->name ?? '') }}"></div>
<div class="field"><label>Slug</label><input name="slug" value="{{ old('slug', $category->slug ?? '') }}" placeholder="auto-generated-if-empty"></div>
<div class="field"><label>Description</label><textarea name="description">{{ old('description', $category->description ?? '') }}</textarea></div>
<div class="row">
    <div class="field" style="flex:1"><label>Sort order</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}"></div>
    <label class="check" style="align-self:center"><input type="checkbox" name="active" value="1" {{ old('active', isset($category) ? $category->active : true) ? 'checked' : '' }}> Active</label>
</div>
