@extends('storefront.layout')

@php
    $title = 'About Us | '.config('store.name');
    $description = 'Learn about '.config('store.name').' and our focus on women and men unstitched wear in Pakistan.';
@endphp

@section('content')
    <section class="page-head">
        <div class="container">
            <span class="section-kicker">Our story</span>
            <h1>About {{ config('store.name') }}</h1>
            <p>Unstitched clothing, thoughtfully selected for Pakistani wardrobes.</p>
        </div>
    </section>

    <div class="container content-page">
        <p>{{ config('store.name') }} is a focused fashion store for women and men unstitched wear. Our goal is simple: make quality fabrics, contemporary prints and embroidered designs easy to discover and order online across Pakistan.</p>

        <h2>What we offer</h2>
        <ul>
            <li>Women unstitched shirts, 2-piece and 3-piece suits.</li>
            <li>Men unstitched fabrics and suit collections.</li>
            <li>Printed, embroidered and seasonal fabric collections.</li>
            <li>Cash on Delivery for convenient nationwide ordering.</li>
        </ul>

        <h2>Our approach</h2>
        <p>We keep the catalogue focused rather than mixing unrelated categories. Every collection is presented with clear product photography, pricing, fabric information and product details so customers can make an informed purchase before ordering.</p>

        <h2>Delivery across Pakistan</h2>
        <p>Orders are processed through our online store and delivered across Pakistan. Shipping charges and free-delivery eligibility are displayed during checkout.</p>
    </div>
@endsection
