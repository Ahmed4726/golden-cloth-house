@extends('storefront.layout')

@php
    $title = 'Contact Us | '.config('store.name');
    $description = 'Contact '.config('store.name').' for help with products, orders, delivery and Cash on Delivery.';
@endphp

@section('content')
    <section class="page-head">
        <div class="container">
            <span class="section-kicker">We are here to help</span>
            <h1>Contact Us</h1>
            <p>Questions about an order or an unstitched collection? Reach out to our team.</p>
        </div>
    </section>

    <div class="container content-page">
        <h2>Customer support</h2>
        <p>For order updates, product questions, delivery assistance or general enquiries, use the contact details below.</p>

        <ul>
            @if (config('store.contact_phone'))<li>Phone: <a href="tel:{{ config('store.contact_phone') }}">{{ config('store.contact_phone') }}</a></li>@endif
            @if (config('store.contact_email'))<li>Email: <a href="mailto:{{ config('store.contact_email') }}">{{ config('store.contact_email') }}</a></li>@endif
            @if (config('store.whatsapp'))<li>WhatsApp: <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('store.whatsapp')) }}" target="_blank" rel="noopener">Chat with us</a></li>@endif
            @if (config('store.address'))<li>Address: {{ config('store.address') }}</li>@endif
        </ul>

        <h2>Before contacting us</h2>
        <p>If you are asking about an existing order, please keep your order number and the phone number used at checkout available. This helps us find your order quickly.</p>

        <div style="margin-top:28px">
            <a class="btn" href="{{ route('order.track') }}">Track an order</a>
            <a class="btn outline" href="{{ route('faq') }}">Read FAQs</a>
        </div>
    </div>
@endsection
