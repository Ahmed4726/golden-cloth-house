@extends('storefront.layout')

@php
    $title = 'Order Confirmed | '.config('store.name');
    $description = 'Your '.config('store.name').' order has been received.';
@endphp

@section('content')
    <div class="container py-5">
        <section class="text-center py-5">
            <div class="display-4 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
            <div class="small text-uppercase text-secondary fw-bold">Order confirmed</div>
            <h1 class="display-5 fw-normal mt-2">Thank you, {{ $order->customer_name }}.</h1>
            <p class="lead text-secondary mx-auto col-lg-7">
                Your order <strong>{{ $order->order_number }}</strong> has been received.
                We will contact you at <strong>{{ $order->customer_phone }}</strong> to confirm delivery.
            </p>

            <div class="card border text-start mx-auto my-4 col-lg-7">
                <div class="card-body">
                    @foreach ($order->items as $item)
                        <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                            <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                            <strong>{{ number_format((float) $item->line_total, 0) }}</strong>
                        </div>
                    @endforeach
                    <div class="d-flex justify-content-between py-2"><span>Delivery</span><strong>{{ $order->shipping_amount > 0 ? number_format((float) $order->shipping_amount, 0).' '.$order->currency : 'Free' }}</strong></div>
                    <div class="d-flex justify-content-between border-top mt-2 pt-3 fs-5"><span>Total to pay on delivery</span><strong>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</strong></div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a class="btn btn-dark" href="{{ route('shop') }}">Continue Shopping</a>
                <a class="btn btn-outline-dark" href="{{ route('order.track') }}">Track Order</a>
            </div>
        </section>
    </div>
@endsection
