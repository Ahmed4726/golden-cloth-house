@extends('storefront.layout')

@php
    $title = 'Shopping Cart | '.config('store.name');
@endphp

@section('content')
    <section class="page-head">
        <div class="container">
            <span class="section-kicker">Your selection</span>
            <h1>Shopping Cart</h1>
        </div>
    </section>

    <div class="container">
        @if (count($items))
            <div class="cart-layout">
                <div>
                    @foreach ($items as $item)
                        @php
                            $image = $item['product']->images()[0] ?? null;
                        @endphp

                        <form method="POST" action="{{ route('cart.update') }}" class="cart-item">
                            @csrf
                            @method('PATCH')

                            @if ($image)
                                <img src="{{ str_starts_with($image, 'http') ? $image : asset('storage/'.$image) }}" alt="{{ $item['product']->name }}">
                            @else
                                <div style="width:90px;height:115px;background:#f2f0ec;display:grid;place-items:center;">GCH</div>
                            @endif

                            <div>
                                <a href="{{ route('shop.product', $item['product']) }}"><strong>{{ $item['product']->name }}</strong></a>
                                <div class="muted">{{ number_format((float) $item['product']->price, 0) }} {{ $item['product']->currency }}</div>
                            </div>

                            <div>
                                <label class="form-label" for="qty-{{ $item['product']->id }}">Qty</label>
                                <input class="input cart-qty" id="qty-{{ $item['product']->id }}" type="number" name="quantities[{{ $item['product']->id }}]" value="{{ $item['quantity'] }}" min="0" max="{{ min($item['product']->stock_quantity, 20) }}">
                            </div>

                            <div class="cart-price">
                                <strong>{{ number_format($item['lineTotal'], 0) }} {{ $item['product']->currency }}</strong>
                                <div>
                                    <button type="submit" class="btn outline" style="padding:6px 9px;margin-top:8px;">Update</button>
                                    <button type="submit" name="remove_product" value="{{ $item['product']->id }}" class="btn outline" style="padding:6px 9px;margin-top:8px;">Remove</button>
                                </div>
                            </div>
                        </form>
                    @endforeach
                </div>

                <aside class="summary">
                    <h2 style="font-size:24px;">Order summary</h2>
                    <div class="summary-row"><span>Subtotal</span><strong>{{ number_format($subtotal, 0) }} {{ config('store.currency') }}</strong></div>
                    <div class="summary-row"><span>Delivery</span><strong>{{ $shipping > 0 ? number_format($shipping, 0).' '.config('store.currency') : 'Free' }}</strong></div>
                    <div class="summary-row summary-total"><span>Total</span><strong>{{ number_format($total, 0) }} {{ config('store.currency') }}</strong></div>
                    <a class="btn full" href="{{ route('checkout') }}">Proceed to Checkout</a>
                    <a class="btn outline full" href="{{ route('shop') }}">Continue Shopping</a>
                    <div class="trust">Secure order processing · Cash on Delivery · No online payment required.</div>
                </aside>
            </div>
        @else
            <div class="empty">
                <h2>Your cart is empty</h2>
                <p class="muted">Add something you love and come back here to complete your order.</p>
                <a class="btn" href="{{ route('shop') }}">Start Shopping</a>
            </div>
        @endif
    </div>
@endsection
