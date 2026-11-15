@extends('storefront.layout')

@php
    $title = 'Shopping Cart | ' . config('store.name');
    $whatsappOrderItems = array_map(
        fn ($item) => '- '.$item['product']->name.' (Qty: '.$item['quantity'].') — '
            .number_format((float) $item['lineTotal'], 0).' '.$item['product']->currency
            ."\n".route('shop.product', $item['product']->slug),
        $items
    );
    $whatsappOrderMessage = "Hello, I would like to place this order:\n"
        .implode("\n", $whatsappOrderItems)
        ."\n\nSubtotal: ".number_format((float) $subtotal, 0).' '.config('store.currency')
        ."\nDelivery: ".($shipping > 0
            ? number_format((float) $shipping, 0).' '.config('store.currency')
            : 'Free')
        ."\nTotal: ".number_format((float) $total, 0).' '.config('store.currency');
@endphp

@section('content')
    <section class="py-5 text-center">
        <div class="container">
            <div class="small text-uppercase text-secondary fw-bold">
                Your selection
            </div>

            <h1 class="display-5 fw-normal">
                Shopping Cart
            </h1>
        </div>
    </section>

    <div class="container pb-5">
        @if (count($items))
            <div class="row g-4">
                <div class="col-lg-8">
                    @foreach ($items as $item)
                        @php
                            $image = $item['product']->images()[0] ?? null;
                            $imageUrl = $image
                                ? $item['product']->imageUrl($image)
                                : asset('images/product-placeholder.svg');
                        @endphp

                        <form
                            method="POST"
                            action="{{ route('cart.update') }}"
                            class="card border mb-3"
                        >
                            @csrf
                            @method('PATCH')

                            <div class="card-body">
                                <div class="row align-items-center g-3">
                                    <div class="col-4 col-sm-3 col-md-2">
                                        <div class="ratio ratio-1x1 bg-light rounded overflow-hidden">
                                            <img
                                                class="w-100 h-100 object-fit-contain"
                                                src="{{ $imageUrl }}"
                                                alt="{{ $item['product']->name }}"
                                                loading="lazy"
                                                onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
                                            >
                                        </div>
                                    </div>

                                    <div class="col-sm">
                                        <a
                                            class="text-dark text-decoration-none"
                                            href="{{ route('shop.product', $item['product']->slug) }}"
                                        >
                                            <strong>{{ $item['product']->name }}</strong>
                                        </a>

                                        <div class="text-secondary">
                                            {{ number_format((float) $item['product']->price, 0) }}
                                            {{ $item['product']->currency }}
                                        </div>
                                    </div>

                                    <div class="col-6 col-sm-2">
                                        <label
                                            class="form-label small fw-bold"
                                            for="qty-{{ $item['product']->id }}"
                                        >
                                            Qty
                                        </label>

                                        <input
                                            class="form-control"
                                            id="qty-{{ $item['product']->id }}"
                                            type="number"
                                            name="quantities[{{ $item['product']->id }}]"
                                            value="{{ $item['quantity'] }}"
                                            min="0"
                                            max="{{ min($item['product']->stock_quantity, 20) }}"
                                        >
                                    </div>

                                    <div class="col-6 col-sm-3 text-sm-end">
                                        <strong>
                                            {{ number_format($item['lineTotal'], 0) }}
                                            {{ $item['product']->currency }}
                                        </strong>

                                        <div class="d-flex flex-wrap gap-2 justify-content-sm-end mt-2">
                                            <button
                                                type="submit"
                                                class="btn btn-outline-dark btn-sm"
                                            >
                                                Update
                                            </button>

                                            <button
                                                type="submit"
                                                name="remove_product"
                                                value="{{ $item['product']->id }}"
                                                class="btn btn-outline-danger btn-sm"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endforeach
                </div>

                <aside class="col-lg-4">
                    <div class="card border">
                        <div class="card-body">
                            <h2 class="h4">Order summary</h2>

                            <div class="d-flex justify-content-between py-2">
                                <span>Subtotal</span>
                                <strong>
                                    {{ number_format($subtotal, 0) }}
                                    {{ config('store.currency') }}
                                </strong>
                            </div>

                            <div class="d-flex justify-content-between py-2">
                                <span>Delivery</span>
                                <strong>
                                    {{ $shipping > 0
                                        ? number_format($shipping, 0) . ' ' . config('store.currency')
                                        : 'Free' }}
                                </strong>
                            </div>

                            <div class="d-flex justify-content-between border-top mt-2 pt-3 fs-5">
                                <span>Total</span>
                                <strong>
                                    {{ number_format($total, 0) }}
                                    {{ config('store.currency') }}
                                </strong>
                            </div>

                            <a
                                class="btn btn-dark w-100 mt-3"
                                href="{{ route('checkout') }}"
                            >
                                Proceed to Checkout
                            </a>

                            @if ($whatsappNumber)
                                <a
                                    class="btn btn-success w-100 mt-2"
                                    href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}?text={{ rawurlencode($whatsappOrderMessage) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <i class="fa-brands fa-whatsapp me-2" aria-hidden="true"></i>Order via WhatsApp
                                </a>
                            @endif

                            <a
                                class="btn btn-outline-dark w-100 mt-2"
                                href="{{ route('shop') }}"
                            >
                                Continue Shopping
                            </a>

                            <div class="bg-light p-3 mt-3 small">
                                Secure order processing · Cash on Delivery · No online payment required.
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        @else
            <div class="text-center border rounded p-5">
                <h2 class="h3">Your cart is empty</h2>

                <p class="text-secondary">
                    Add something you love and come back here to complete your order.
                </p>

                <a
                    class="btn btn-dark"
                    href="{{ route('shop') }}"
                >
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
@endsection
