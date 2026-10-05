@extends('storefront.layout')

@php
    $title = 'Checkout | '.config('store.name');
@endphp

@section('content')
    <section class="page-head">
        <span class="eyebrow">CASH ON DELIVERY</span>
        <h1 style="font-size:clamp(42px,6vw,64px);margin:10px 0;">Checkout</h1>
        <p>Enter your delivery details. You will pay the courier when your order arrives.</p>
    </section>

    <div class="checkout-layout">
        <form class="checkout-card" method="POST" action="{{ route('checkout.store') }}">
            @csrf

            <h2 style="font-size:25px;">Delivery information</h2>

            <div class="field">
                <label class="form-label" for="customer_name">Full name *</label>
                <input class="input" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required autocomplete="name">
            </div>

            <div class="field">
                <label class="form-label" for="customer_phone">Phone number *</label>
                <input class="input" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required autocomplete="tel" placeholder="03XXXXXXXXX">
            </div>

            <div class="field">
                <label class="form-label" for="customer_email">Email address</label>
                <input class="input" id="customer_email" type="email" name="customer_email" value="{{ old('customer_email') }}" autocomplete="email">
            </div>

            <div class="field">
                <label class="form-label" for="shipping_city">City *</label>
                <input class="input" id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" required autocomplete="address-level2">
            </div>

            <div class="field">
                <label class="form-label" for="shipping_area">Area / Town</label>
                <input class="input" id="shipping_area" name="shipping_area" value="{{ old('shipping_area') }}" autocomplete="address-level3">
            </div>

            <div class="field">
                <label class="form-label" for="shipping_address">Complete delivery address *</label>
                <textarea class="textarea" id="shipping_address" name="shipping_address" required autocomplete="street-address">{{ old('shipping_address') }}</textarea>
            </div>

            <div class="field">
                <label class="form-label" for="customer_note">Order note</label>
                <textarea class="textarea" id="customer_note" name="customer_note" placeholder="Optional delivery instructions">{{ old('customer_note') }}</textarea>
            </div>

            <div class="trust">
                <strong>Payment method: Cash on Delivery</strong>
                <br>
                No card or online payment is required. Pay the delivery rider when your parcel arrives.
            </div>

            <button class="btn full" type="submit">Place Order · {{ number_format($total, 0) }} {{ config('store.currency') }}</button>
        </form>

        <aside class="summary">
            <h2 style="font-size:24px;">Your order</h2>

            @foreach ($items as $item)
                <div class="summary-row">
                    <span>{{ $item['product']->name }} × {{ $item['quantity'] }}</span>
                    <strong>{{ number_format($item['lineTotal'], 0) }}</strong>
                </div>
            @endforeach

            <div class="summary-row"><span>Subtotal</span><strong>{{ number_format($subtotal, 0) }} {{ config('store.currency') }}</strong></div>
            <div class="summary-row"><span>Delivery</span><strong>{{ $shipping > 0 ? number_format($shipping, 0).' '.config('store.currency') : 'Free' }}</strong></div>
            <div class="summary-row summary-total"><span>Total</span><strong>{{ number_format($total, 0) }} {{ config('store.currency') }}</strong></div>

            <a class="btn secondary full" href="{{ route('cart.index') }}">Back to Cart</a>
        </aside>
    </div>
@endsection
