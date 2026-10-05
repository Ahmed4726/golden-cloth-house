<div class="field"><label>Name</label><input name="name" required value="{{ old('name', $brand->name ?? '') }}"></div>
<div class="field"><label>Slug</label><input name="slug" value="{{ old('slug', $brand->slug ?? '') }}" placeholder="auto-generated-if-empty"></div>
<div class="field"><label>Description</label><textarea name="description">{{ old('description', $brand->description ?? '') }}</textarea></div>
<div class="row">
    <div class="field" style="flex:1"><label>Sort order</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $brand->sort_order ?? 0) }}"></div>
    <label class="check" style="align-self:center"><input type="checkbox" name="active" value="1" {{ old('active', isset($brand) ? $brand->active : true) ? 'checked' : '' }}> Active</label>
</div>
<div class="field"><label>Logo</label><input type="file" name="logo" accept="image/jpeg,image/png,image/webp"></div>
@if (!empty($brand?->logo_path))<img src="{{ asset('storage/'.$brand->logo_path) }}" alt="{{ $brand->name }}" style="width:90px;height:90px;object-fit:contain">@endif
