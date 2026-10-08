@extends('storefront.layout')

@php
    $title = 'Contact Us | '.config('store.name');
    $description = 'Contact '.config('store.name').' for help with products, orders, delivery and Cash on Delivery.';
@endphp

@section('content')
    <section class="py-5 text-center">
        <div class="container">
            <div class="small text-uppercase text-secondary fw-bold">We are here to help</div>
            <h1 class="display-5 fw-normal">Contact Us</h1>
            <p class="text-secondary mb-0">Questions about an order or an unstitched collection? Reach out to our team.</p>
        </div>
    </section>

    <div class="container pb-5">
        <div class="mx-auto col-lg-8">
            <h2 class="h3">Customer support</h2>
            <p class="text-secondary">For order updates, product questions, delivery assistance or general enquiries, use the contact details below.</p>

            <ul class="list-group mb-5">
                @if (config('store.contact_phone'))<li class="list-group-item">Phone: <a href="tel:{{ config('store.contact_phone') }}">{{ config('store.contact_phone') }}</a></li>@endif
                @if (config('store.contact_email'))<li class="list-group-item">Email: <a href="mailto:{{ config('store.contact_email') }}">{{ config('store.contact_email') }}</a></li>@endif
                @if (config('store.whatsapp'))<li class="list-group-item">WhatsApp: <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('store.whatsapp')) }}" target="_blank" rel="noopener">Chat with us</a></li>@endif
                @if (config('store.address'))<li class="list-group-item">Address: {{ config('store.address') }}</li>@endif
            </ul>

            <h2 class="h3">Before contacting us</h2>
            <p class="text-secondary">If you are asking about an existing order, please keep your order number and the phone number used at checkout available. This helps us find your order quickly.</p>

            <div class="d-flex flex-wrap gap-2 mt-4">
                <a class="btn btn-dark" href="{{ route('order.track') }}">Track an order</a>
                <a class="btn btn-outline-dark" href="{{ route('faq') }}">Read FAQs</a>
            </div>
        </div>
    </div>
@endsection
