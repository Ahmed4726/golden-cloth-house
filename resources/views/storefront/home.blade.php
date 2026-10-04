@extends('storefront.layout')
@section('content')
<section class="hero"><div><span class="eyebrow">GOLDEN CLOTH HOUSE</span><h1>Elegant clothing, made for your everyday style.</h1><p>Discover our latest collection and shop directly from Golden Cloth House.</p><a class="shop-btn" href="{{ route('shop') }}">Shop the collection</a></div></section>
<section class="section"><div class="section-head"><div><span class="eyebrow">LATEST</span><h2>New arrivals</h2></div><a href="{{ route('shop') }}">View all →</a></div><div class="product-grid">@forelse($products as $product) @include('storefront.product-card',['product'=>$product]) @empty<div class="empty">Products will appear here as you add them from the admin panel.</div>@endforelse</div></section>
@endsection