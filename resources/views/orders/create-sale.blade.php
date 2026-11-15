@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Record in-store sale</h1>
            <div class="text-secondary">Record an offline sale and deduct the sold quantity from inventory.</div>
        </div>
        <a class="btn btn-outline-dark" href="{{ route('products.index') }}">Back to Products</a>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-12 col-xl-7">
            <form class="card border-0 shadow-sm" method="POST" action="{{ route('products.sales.store', $product) }}">
                @csrf
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Sale details</h2>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="quantity">Quantity sold</label>
                        <input
                            class="form-control"
                            id="quantity"
                            name="quantity"
                            type="number"
                            min="1"
                            max="{{ $product->stock_quantity }}"
                            value="{{ old('quantity', 1) }}"
                            required
                            @disabled($product->stock_quantity < 1)
                        >
                        <div class="form-text">Available stock: {{ $product->stock_quantity }}. Sales above available stock are not allowed.</div>
                    </div>

                    <h2 class="h5 fw-bold mb-3">Customer details <span class="text-secondary fw-normal">(optional)</span></h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="customer_name">Customer name</label>
                            <input class="form-control" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" maxlength="120" autocomplete="name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="customer_phone">Phone</label>
                            <input class="form-control" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" maxlength="40" autocomplete="tel">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="customer_email">Email</label>
                            <input class="form-control" id="customer_email" name="customer_email" type="email" value="{{ old('customer_email') }}" maxlength="190" autocomplete="email">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="shipping_address">Address</label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" maxlength="500" autocomplete="street-address">{{ old('shipping_address') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="shipping_city">City</label>
                            <input class="form-control" id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" maxlength="120" autocomplete="address-level2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="shipping_area">Area</label>
                            <input class="form-control" id="shipping_area" name="shipping_area" value="{{ old('shipping_area') }}" maxlength="160" autocomplete="address-level3">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="customer_note">Notes</label>
                            <textarea class="form-control" id="customer_note" name="customer_note" rows="2" maxlength="1000">{{ old('customer_note') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white p-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <span class="text-secondary small">Payment is recorded as paid in cash.</span>
                    <button class="btn btn-dark" type="submit" @disabled($product->stock_quantity < 1)>Record Sale</button>
                </div>
            </form>

            @if ($product->stock_quantity < 1)
                <div class="alert alert-warning mt-3 mb-0" role="alert">This product is out of stock. Restock it before recording another sale.</div>
            @endif
        </div>

        <div class="col-12 col-xl-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Product</h2>
                    <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                        <span class="text-secondary">Name</span>
                        <strong class="text-end">{{ $product->name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                        <span class="text-secondary">SKU</span>
                        <strong>{{ $product->sku ?: '—' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                        <span class="text-secondary">Unit price</span>
                        <strong>{{ number_format((float) $product->price, 0) }} {{ $product->currency }}</strong>
                    </div>
                    <div class="d-flex justify-content-between gap-3 py-2">
                        <span class="text-secondary">Available stock</span>
                        <strong>{{ $product->stock_quantity }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
