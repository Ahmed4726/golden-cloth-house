@extends('storefront.layout')

@php
    $title = 'Order Confirmed | '.config('store.name');
    $description = 'Your Golden Cloth House order has been received.';
@endphp

@section('content')
    <section class="success">
        <div class="success-icon">✓</div>
        <span class="eyebrow">ORDER CONFIRMED</span>
        <h1 style="font-size:clamp(38px,6vw,64px);margin:10px 0;">Thank you, {{ $order->customer_name }}.</h1>
        <p style="font-size:18px;color:var(--muted);max-width:650px;margin:0 auto;">
            Your order <strong>{{ $order->order_number }}</strong> has been received.
            We will contact you at <strong>{{ $order->customer_phone }}</strong> to confirm delivery.
        </p>

        <div class="summary" style="max-width:560px;margin:30px auto;text-align:left;position:static;">
            @foreach ($order->items as $item)
                <div class="summary-row">
                    <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                    <strong>{{ number_format((float) $item->line_total, 0) }}</strong>
                </div>
            @endforeach
            <div class="summary-row"><span>Delivery</span><strong>{{ $order->shipping_amount > 0 ? number_format((float) $order->shipping_amount, 0).' '.$order->currency : 'Free' }}</strong></div>
            <div class="summary-row summary-total"><span>Total to pay on delivery</span><strong>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</strong></div>
        </div>

        <a class="btn" href="{{ route('shop') }}">Continue Shopping</a>
    </section>
@endsection
