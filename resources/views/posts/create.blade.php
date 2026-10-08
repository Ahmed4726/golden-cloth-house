@extends('layouts.app')
@section('content')
    <div class="mb-4"><h1 class="h2 fw-bold mb-1">Create social post</h1><div class="text-secondary">Select a product, choose the social accounts, and publish the product as a normal social post.</div></div>
    <form class="card border-0 shadow-sm" method="post" action="{{ route('posts.store') }}">
        @csrf
        <div class="card-body p-4">
            <div class="mb-4"><label class="form-label fw-semibold">Product</label><select class="form-select" name="product_id" id="product-select"><option value="">— Custom post —</option>@foreach ($products as $product)<option value="{{ $product->id }}" data-title="{{ $product->name }}" data-description="{{ $product->description }}" data-images='@json($product->images())' @selected(old('product_id') == $product->id)>{{ $product->name }} @if($product->brand) · {{ $product->brand->name }} @endif</option>@endforeach</select><div class="form-text">Selecting a product automatically uses its title, description and uploaded/external images.</div></div>
            <div class="mb-3"><label class="form-label fw-semibold">Post title</label><input class="form-control" id="post-title" name="title" value="{{ old('title') }}" placeholder="Automatically filled from product"></div>
            <div class="mb-3"><label class="form-label fw-semibold">Post description / caption</label><textarea class="form-control" id="post-caption" name="caption" rows="6" placeholder="Automatically filled from product description">{{ old('caption') }}</textarea></div>
            <div class="mb-4"><label class="form-label fw-semibold">Images</label><textarea class="form-control" id="media-urls" name="media_urls" rows="4" placeholder="Product image URLs will appear here automatically, one per line.">{{ old('media_urls') }}</textarea><div id="product-preview" class="row row-cols-3 row-cols-md-6 g-2 mt-1"></div></div>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label fw-semibold">Media type</label><select class="form-select" name="media_type"><option value="image">Image post</option><option value="video">Video post</option></select></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Video URL</label><input class="form-control" type="url" name="video_url" value="{{ old('video_url') }}" placeholder="https://cdn.example.com/video.mp4"></div>
                <div class="col-12"><label class="form-label fw-semibold">Publish to social media</label><div class="row row-cols-1 row-cols-md-2 g-2">@forelse ($connections as $connection)<div class="col"><label class="form-check border rounded p-3"><input class="form-check-input" type="checkbox" name="connections[]" value="{{ $connection->id }}" @checked(in_array($connection->id, old('connections', [])))> <span class="form-check-label">{{ ucfirst($connection->provider->value) }} · {{ $connection->name }}</span></label></div>@empty<div class="text-secondary">Connect at least one social account first.</div>@endforelse</div></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Scheduled time</label><input class="form-control" type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"></div>
            </div>
        </div>
        <div class="card-footer bg-white p-4 d-flex flex-wrap gap-2"><button class="btn btn-dark" name="action" value="publish">Publish selected accounts</button><button class="btn btn-outline-dark" name="action" value="schedule">Schedule</button><button class="btn btn-outline-dark" name="action" value="draft">Save Draft</button></div>
    </form>
@endsection

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
        if (!option || !option.value) { preview.innerHTML = ''; return; }
        const images = JSON.parse(option.dataset.images || '[]');
        title.value = option.dataset.title || '';
        caption.value = option.dataset.description || '';
        media.value = images.join('\n');
        preview.innerHTML = images.map((url) => `<div class="col"><div class="ratio ratio-3x4 bg-light rounded overflow-hidden"><img class="w-100 h-100 object-fit-contain" src="${url}" alt="Product image"></div></div>`).join('');
    }
    select.addEventListener('change', loadProduct);
    loadProduct();
})();
</script>
@endpush
