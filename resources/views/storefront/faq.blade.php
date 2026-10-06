@extends('storefront.layout')

@php
    $title = 'FAQs | '.config('store.name');
    $description = 'Frequently asked questions about ordering, Cash on Delivery, delivery and unstitched wear from '.config('store.name').'.';
@endphp

@section('content')
    <section class="page-head">
        <div class="container">
            <span class="section-kicker">Customer care</span>
            <h1>Frequently Asked Questions</h1>
            <p>Everything you need to know before placing your order.</p>
        </div>
    </section>

    <div class="container content-page">
        <div class="faq-item"><details open><summary>Do you offer Cash on Delivery?</summary><div>Yes. Cash on Delivery is the primary payment method for the store and is available across Pakistan, subject to the checkout eligibility shown for your order.</div></details></div>
        <div class="faq-item"><details><summary>Do you sell stitched or unstitched clothing?</summary><div>Our store is focused on women and men unstitched wear. Product titles and descriptions clearly identify the pieces included in each fabric set.</div></details></div>
        <div class="faq-item"><details><summary>What is included in a 2-piece or 3-piece suit?</summary><div>The exact pieces and fabric lengths are listed on each product detail page. Please review the Product Details section before ordering.</div></details></div>
        <div class="faq-item"><details><summary>How can I track my order?</summary><div>Use the Track Order page and enter your order number and phone number used during checkout.</div></details></div>
        <div class="faq-item"><details><summary>How much is delivery?</summary><div>Delivery charges are calculated at checkout. Orders above the configured free-delivery threshold qualify for free delivery.</div></details></div>
        <div class="faq-item"><details><summary>Can I change or cancel my order?</summary><div>Contact us as soon as possible after placing the order. Once an order has entered dispatch, changes may no longer be possible.</div></details></div>
        <div class="faq-item"><details><summary>Why can product colours look slightly different?</summary><div>Photography, lighting and individual screen settings can affect how colours appear. Small variations are therefore possible.</div></details></div>
    </div>
@endsection
