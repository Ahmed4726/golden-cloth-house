@extends('storefront.layout')

@php
    $title = 'Checkout | '.config('store.name');
@endphp

@section('content')
    <section class="py-5 text-center">
        <div class="container">
            <div class="small text-uppercase text-secondary fw-bold">Cash on Delivery</div>
            <h1 class="display-5 fw-normal">Checkout</h1>
            <p class="text-secondary mb-0">Enter your delivery details. You will pay the courier when your order arrives.</p>
        </div>
    </section>

    <div class="container pb-5">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <div class="row g-4">
            <div class="col-lg-8">
                <form class="card border" method="POST" action="{{ route('checkout.store') }}">
                    @csrf
                    <div class="card-body p-4">
                        <h2 class="h4 mb-4">Delivery information</h2>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="customer_name">Full name *</label>
                            <input class="form-control" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required autocomplete="name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="customer_phone">Phone number *</label>
                            <input class="form-control" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required autocomplete="tel" placeholder="03XXXXXXXXX">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="customer_email">Email address</label>
                            <input class="form-control" id="customer_email" type="email" name="customer_email" value="{{ old('customer_email') }}" autocomplete="email">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="shipping_city">City *</label>
                                <input class="form-control" id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" required autocomplete="address-level2">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="shipping_area">Area / Town</label>
                                <input class="form-control" id="shipping_area" name="shipping_area" value="{{ old('shipping_area') }}" autocomplete="address-level3">
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label fw-semibold" for="shipping_address">Complete delivery address *</label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="4" required autocomplete="street-address">{{ old('shipping_address') }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="customer_note">Order note</label>
                            <textarea class="form-control" id="customer_note" name="customer_note" rows="3" placeholder="Optional delivery instructions">{{ old('customer_note') }}</textarea>
                        </div>

                        <div class="alert alert-light border">
                            <strong>Payment method: Cash on Delivery</strong><br>
                            No card or online payment is required. Pay the delivery rider when your parcel arrives.
                        </div>
                        <button class="btn btn-dark w-100 py-2" type="submit">Place Order · {{ number_format($total, 0) }} {{ config('store.currency') }}</button>
                    </div>
                </form>
            </div>

            <aside class="col-lg-4">
                <div class="card border">
                    <div class="card-body">
                        <h2 class="h4">Your order</h2>
                        @foreach ($items as $item)
                            <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                                <span>{{ $item['product']->name }} × {{ $item['quantity'] }}</span>
                                <strong>{{ number_format($item['lineTotal'], 0) }}</strong>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between py-2">
                            <span>Subtotal</span>
                            <strong>{{ number_format($subtotal, 0) }} {{ config('store.currency') }}</strong>
                        </div>
                        @if ($coupon)
                            <div class="d-flex justify-content-between align-items-center py-2 text-success">
                                <span>Coupon ({{ $coupon->code }})</span>
                                <strong>−{{ number_format($coupon_discount, 0) }} {{ config('store.currency') }}</strong>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between py-2">
                            <span>Delivery</span>
                            <strong>{{ $shipping > 0 ? number_format($shipping, 0).' '.config('store.currency') : 'Free' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-top mt-2 pt-3 fs-5">
                            <span>Total</span>
                            <strong>{{ number_format($total, 0) }} {{ config('store.currency') }}</strong>
                        </div>
                        <a class="btn btn-outline-dark w-100 mt-3" href="{{ route('cart.index') }}">Back to Cart</a>
                        @if ($coupon)
                            <form method="POST" action="{{ route('checkout.coupon.destroy') }}" class="mt-3">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-secondary w-100" type="submit">Remove coupon</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('checkout.coupon.store') }}" class="mt-3">
                                @csrf
                                <label class="form-label fw-semibold" for="coupon_code">Coupon code</label>
                                <div class="input-group">
                                    <input class="form-control" id="coupon_code" name="coupon_code" value="{{ old('coupon_code') }}" maxlength="40" placeholder="Enter code" required>
                                    <button class="btn btn-outline-dark" type="submit">Apply</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
