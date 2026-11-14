@extends('storefront.layout')

@php
    $title = 'Track Order | '.config('store.name');
@endphp

@section('content')
    <section class="bg-body-tertiary py-5">
        <div class="container">
            <header class="mb-4">
                <div class="small text-uppercase text-secondary fw-semibold mb-2">Customer care</div>
                <h1 class="display-6 fw-normal mb-2">Track your order</h1>
                <p class="text-secondary mb-0">Enter your order number and the phone number used at checkout.</p>
            </header>

            <div class="row g-4 align-items-stretch">
                <div class="col-lg-7">
                    <form class="card h-100 shadow-sm" method="POST" action="{{ route('order.track.submit') }}">
                        @csrf
                        <div class="card-body p-4">
                            <h2 class="h4 mb-4">Find your order</h2>
                            <div class="mb-3">
                                <label class="form-label" for="order_number">Order number</label>
                                <input class="form-control" id="order_number" name="order_number" value="{{ old('order_number') }}" placeholder="GCH-261005-ABC123" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="phone">Phone number</label>
                                <input class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="03XXXXXXXXX" required>
                            </div>
                            <button class="btn btn-dark" type="submit">Track order</button>
                        </div>
                    </form>
                </div>

                <div class="col-lg-5">
                    @isset($order)
                        <aside class="card h-100 shadow-sm">
                            <div class="card-body p-4">
                                <div class="small text-secondary">Order {{ $order->order_number }}</div>
                                <h2 class="h3 text-capitalize mt-1 mb-4">{{ ucfirst($order->status) }}</h2>
                                <dl class="row mb-4">
                                    <dt class="col-5">Total</dt>
                                    <dd class="col-7 text-end">{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</dd>
                                    <dt class="col-5">Payment</dt>
                                    <dd class="col-7 text-end">Cash on Delivery</dd>
                                    <dt class="col-5">City</dt>
                                    <dd class="col-7 text-end mb-0">{{ $order->shipping_city }}</dd>
                                </dl>
                                <div class="alert alert-info mb-0">We will contact you on {{ $order->customer_phone }} for delivery confirmation.</div>
                            </div>
                        </aside>
                    @else
                        <aside class="card h-100 shadow-sm">
                            <div class="card-body p-4">
                                <h2 class="h4 mb-3">Need help?</h2>
                                <p class="text-secondary">If you cannot find your order, contact our customer support team with your order number and phone number.</p>
                                <a class="btn btn-outline-dark" href="{{ route('contact') }}">Contact us</a>
                            </div>
                        </aside>
                    @endisset
                </div>
            </div>
        </div>
    </section>
@endsection
