<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold" for="fabric-name">Fabric name</label>
        <input class="form-control" id="fabric-name" name="name" maxlength="120" value="{{ old('name', $fabric->name ?? '') }}" required>
        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="fabric-sort-order">Sort order</label>
        <input class="form-control" id="fabric-sort-order" name="sort_order" type="number" min="0" max="100000" value="{{ old('sort_order', $fabric->sort_order ?? 0) }}">
    </div>
    <div class="col-12">
        <div class="form-check">
            <input type="hidden" name="active" value="0">
            <input class="form-check-input" id="fabric-active" name="active" type="checkbox" value="1" @checked(old('active', $fabric->active ?? true))>
            <label class="form-check-label" for="fabric-active">Available for selection</label>
        </div>
    </div>
</div>
