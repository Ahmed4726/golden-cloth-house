@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div><h1 class="h2 fw-bold mb-1">Edit Product</h1><div class="text-secondary">Update product details, inventory and images.</div></div>
        <a class="btn btn-outline-dark" href="{{ route('products.index') }}">Back</a>
    </div>

    <form class="card border-0 shadow-sm" method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body p-4">@include('products.form', ['product' => $product])</div>
        <div class="card-footer bg-white p-4"><button class="btn btn-dark" type="submit">Save Changes</button></div>
    </form>

    @foreach ($product->media_paths ?? [] as $imageIndex => $path)
        <form id="remove-product-media-{{ $imageIndex }}" method="POST" action="{{ route('products.images.remove', $product) }}" onsubmit="return confirm('Remove this image?');">
            @csrf
            @method('DELETE')
            <input type="hidden" name="type" value="media">
            <input type="hidden" name="value" value="{{ $path }}">
        </form>
    @endforeach

    @foreach ($product->image_urls ?? [] as $imageIndex => $url)
        <form id="remove-product-url-{{ $imageIndex }}" method="POST" action="{{ route('products.images.remove', $product) }}" onsubmit="return confirm('Remove this image URL?');">
            @csrf
            @method('DELETE')
            <input type="hidden" name="type" value="url">
            <input type="hidden" name="value" value="{{ $url }}">
        </form>
    @endforeach
@endsection
