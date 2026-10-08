<div class="row g-3">
    <div class="col-12">
        <label
            class="form-label fw-semibold"
            for="slide-title"
        >
            Title
        </label>

        <input
            class="form-control"
            id="slide-title"
            name="title"
            required
            maxlength="160"
            value="{{ old('title', $slide->title ?? '') }}"
        >
    </div>

    <div class="col-12">
        <label
            class="form-label fw-semibold"
            for="slide-subtitle"
        >
            Subtitle
        </label>

        <input
            class="form-control"
            id="slide-subtitle"
            name="subtitle"
            maxlength="160"
            value="{{ old('subtitle', $slide->subtitle ?? '') }}"
        >
    </div>

    <div class="col-12">
        <label
            class="form-label fw-semibold"
            for="slide-description"
        >
            Description
        </label>

        <textarea
            class="form-control"
            id="slide-description"
            name="description"
            rows="5"
        >{{ old('description', $slide->description ?? '') }}</textarea>
    </div>

    <div class="col-md-6">
        <label
            class="form-label fw-semibold"
            for="button-text"
        >
            Button text
        </label>

        <input
            class="form-control"
            id="button-text"
            name="button_text"
            value="{{ old('button_text', $slide->button_text ?? 'Shop now') }}"
        >
    </div>

    <div class="col-md-6">
        <label
            class="form-label fw-semibold"
            for="button-url"
        >
            Button URL
        </label>

        <input
            class="form-control"
            id="button-url"
            name="button_url"
            value="{{ old('button_url', $slide->button_url ?? route('shop')) }}"
        >
    </div>

    <div class="col-md-4">
        <label
            class="form-label fw-semibold"
            for="slide-sort"
        >
            Sort order
        </label>

        <input
            class="form-control"
            id="slide-sort"
            type="number"
            min="0"
            name="sort_order"
            value="{{ old('sort_order', $slide->sort_order ?? 0) }}"
        >
    </div>

    <div class="col-md-8">
        <label
            class="form-label fw-semibold"
            for="slide-image"
        >
            Hero image
        </label>

        <input
            class="form-control"
            id="slide-image"
            type="file"
            name="image"
            accept="image/jpeg,image/png,image/webp"
        >

        <div class="form-text">
            Recommended: 1800×900 or larger. Maximum 10 MB.
        </div>
    </div>

    @if (!empty($slide?->image_path))
        <div class="col-12">
            <label class="form-label fw-semibold">
                Current hero image
            </label>

            <div class="ratio ratio-2x1 bg-light rounded overflow-hidden">
                <img
                    class="w-100 h-100 object-fit-cover"
                    src="{{ $slide->imageUrl() }}"
                    alt="{{ $slide->title }}"
                >
            </div>
        </div>
    @endif

    <div class="col-12">
        <div class="form-check">
            <input
                class="form-check-input"
                id="slide-active"
                type="checkbox"
                name="active"
                value="1"
                {{ old('active', isset($slide) ? $slide->active : true) ? 'checked' : '' }}
            >

            <label
                class="form-check-label"
                for="slide-active"
            >
                Active
            </label>
        </div>
    </div>
</div>
