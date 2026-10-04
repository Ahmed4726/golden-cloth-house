@extends('storefront.layout')
@section('content')
<section class="product-detail">
<div class="gallery">@php($images=array_values(array_unique(array_merge($product->media_paths??[],$product->image_urls??[])))) @if($images)<div class="main-image"><img src="{{ str_starts_with($images[0],'http')?$images[0]:asset('storage/'.$images[0]) }}" alt="{{ $product->name }}"></div><div class="thumbs">@foreach($images as $image)<img src="{{ str_starts_with($image,'http')?$image:asset('storage/'.$image) }}" alt="{{ $product->name }}">@endforeach</div>@else<div class="main-image placeholder">Golden Cloth House</div>@endif</div>
<div class="details"><span class="eyebrow">{{ $product->category ?: 'COLLECTION' }}</span><h1>{{ $product->name }}</h1>@if($product->price)<div class="price">{{ number_format((float)$product->price,2) }} {{ $product->currency }}</div>@endif<div class="description">{!! nl2br(e($product->description)) !!}</div>@if($product->sku)<div class="sku">SKU: {{ $product->sku }}</div>@endif<a class="shop-btn" href="mailto:{{ config('mail.from.address') }}?subject={{ urlencode('Order: '.$product->name) }}">Contact to order</a></div>
</section>
@endsection