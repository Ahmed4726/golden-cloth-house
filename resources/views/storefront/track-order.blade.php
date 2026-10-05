@extends('storefront.layout')

@php
    $title = 'Track Order | '.config('store.name');
@endphp

@section('content')
    <section class="page-head">
        <span class="eyebrow">CUSTOMER CARE</span>
        <h1 style="font-size:clamp(42px,6vw,64px);margin:10px 0;">Track your order</h1>
        <p>Enter your order number and the phone number used at checkout.</p>
    </section>

    <div class="checkout-layout">
        <form class="checkout-card" method="POST" action="{{ route('order.track') }}">
            @csrf

            <div class="field">
                <label class="form-label" for="order_number">Order number</label>
                <input class="input" id="order_number" name="order_number" value="{{ old('order_number') }}" placeholder="GCH-261005-ABC123" required>
            </div>

            <div class="field">
                <label class="form-label" for="phone">Phone number</label>
                <input class="input" id="phone" name="phone" value="{{ old('phone') }}" placeholder="03XXXXXXXXX" required>
            </div>

            <button class="btn" type="submit">Track Order</button>
        </form>

        @isset($order)
            <aside class="summary" style="position:static;">
                <div class="muted">Order {{ $order->order_number }}</div>
                <h2 style="font-size:30px;">{{ ucfirst($order->status) }}</h2>
                <div class="summary-row"><span>Total</span><strong>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</strong></div>
                <div class="summary-row"><span>Payment</span><strong>Cash on Delivery</strong></div>
                <div class="summary-row"><span>City</span><strong>{{ $order->shipping_city }}</strong></div>
                <div class="trust">We will contact you on {{ $order->customer_phone }} for delivery confirmation.</div>
            </aside>
        @endisset
    </div>
@endsection
