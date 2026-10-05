@extends('layouts.app')

@section('content')
<div class="top">
    <div>
        <h1>Create social post</h1>
        <div class="muted">Select a product, choose the social accounts, and publish the product as a normal social post.</div>
    </div>
</div>

<form class="card form" method="post" action="{{ route('posts.store') }}">
    @csrf

    <div class="field">
        <label>Product</label>
        <select name="product_id" id="product-select">
            <option value="">— Custom post —</option>
            @foreach ($products as $product)
                <option
                    value="{{ $product->id }}"
                    data-title="{{ $product->name }}"
                    data-description="{{ $product->description }}"
                    data-images='@json($product->images())'
                    @selected(old('product_id') == $product->id)
                >
                    {{ $product->name }} @if($product->brand) · {{ $product->brand->name }} @endif
                </option>
            @endforeach
        </select>
        <small class="muted">Selecting a product automatically uses its title, description and uploaded/external images.</small>
    </div>

    <div class="field"><label>Post title</label><input id="post-title" name="title" value="{{ old('title') }}" placeholder="Automatically filled from product"></div>
    <div class="field"><label>Post description / caption</label><textarea id="post-caption" name="caption" placeholder="Automatically filled from product description">{{ old('caption') }}</textarea></div>

    <div class="field">
        <label>Images</label>
        <textarea id="media-urls" name="media_urls" placeholder="Product image URLs will appear here automatically, one per line.">{{ old('media_urls') }}</textarea>
        <div id="product-preview" class="row" style="flex-wrap:wrap;margin-top:10px;"></div>
    </div>

    <div class="field">
        <label>Media type</label>
        <select name="media_type">
            <option value="image">Image post</option>
            <option value="video">Video post</option>
        </select>
    </div>

    <div class="field"><label>Video URL (only for video posts)</label><input type="url" name="video_url" value="{{ old('video_url') }}" placeholder="https://cdn.example.com/video.mp4"></div>

    <div class="field">
        <label>Publish to social media</label>
        <div class="checkgrid">
            @forelse ($connections as $connection)
                <label class="check">
                    <input type="checkbox" name="connections[]" value="{{ $connection->id }}" @checked(in_array($connection->id, old('connections', [])))>
                    {{ ucfirst($connection->provider->value) }} · {{ $connection->name }}
                </label>
            @empty
                <div class="muted">Connect at least one social account first.</div>
            @endforelse
        </div>
    </div>

    <div class="field"><label>Scheduled time (only for Schedule)</label><input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"></div>

    <div class="row">
        <button class="btn" name="action" value="publish">Publish selected accounts</button>
        <button class="btn secondary" name="action" value="schedule">Schedule</button>
        <button class="btn secondary" name="action" value="draft">Save Draft</button>
    </div>
</form>

@push('scripts')
<script>
    (() => {
        const select = document.getElementById('product-select');
        const title = document.getElementById('post-title');
        const caption = document.getElementById('post-caption');
        const media = document.getElementById('media-urls');
        const preview = document.getElementById('product-preview');

        function loadProduct() {
            const option = select.options[select.selectedIndex];
            if (!option || !option.value) {
                preview.innerHTML = '';
                return;
            }

            const images = JSON.parse(option.dataset.images || '[]');
            title.value = option.dataset.title || '';
            caption.value = option.dataset.description || '';
            media.value = images.join('\n');
            preview.innerHTML = images.map((url) => `<img src="${url}" alt="Product image" style="width:90px;height:110px;object-fit:cover;border-radius:8px;">`).join('');
        }

        select.addEventListener('change', loadProduct);
        loadProduct();
    })();
</script>
@endpush
@endsection
