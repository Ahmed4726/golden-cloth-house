<div class="field">
    <label>Title</label>
    <input name="title" required maxlength="160" value="{{ old('title', $slide->title ?? '') }}">
</div>

<div class="field">
    <label>Subtitle</label>
    <input name="subtitle" maxlength="160" value="{{ old('subtitle', $slide->subtitle ?? '') }}">
</div>

<div class="field">
    <label>Description</label>
    <textarea name="description">{{ old('description', $slide->description ?? '') }}</textarea>
</div>

<div class="row">
    <div class="field" style="flex:1;">
        <label>Button text</label>
        <input name="button_text" value="{{ old('button_text', $slide->button_text ?? 'Shop now') }}">
    </div>

    <div class="field" style="flex:1;">
        <label>Button URL</label>
        <input name="button_url" value="{{ old('button_url', $slide->button_url ?? route('shop')) }}">
    </div>
</div>

<div class="row">
    <div class="field" style="flex:1;">
        <label>Sort order</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $slide->sort_order ?? 0) }}">
    </div>

    <div class="field" style="flex:2;">
        <label>Hero image</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        <small class="muted">Recommended: 1800×900 or larger. Maximum 10 MB.</small>
    </div>
</div>

@if (!empty($slide?->image_path))
    <div class="field">
        <img src="{{ asset('storage/'.$slide->image_path) }}" alt="{{ $slide->title }}" style="width:100%;max-width:700px;aspect-ratio:2/1;object-fit:cover;border-radius:12px;">
    </div>
@endif

<label class="check">
    <input type="checkbox" name="active" value="1" {{ old('active', isset($slide) ? $slide->active : true) ? 'checked' : '' }}>
    Active
</label>
