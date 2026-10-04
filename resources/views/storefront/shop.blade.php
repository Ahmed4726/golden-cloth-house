@extends('storefront.layout')
@section('content')
<section class="page-head"><span class="eyebrow">COLLECTION</span><h1>Shop all products</h1><p>Browse the Golden Cloth House catalogue.</p></section>
<section class="section"><div class="product-grid">@forelse($products as $product) @include('storefront.product-card',['product'=>$product]) @empty<div class="empty">No products are available yet.</div>@endforelse</div><div class="pagination">{{ $products->links() }}</div></section>
@endsection