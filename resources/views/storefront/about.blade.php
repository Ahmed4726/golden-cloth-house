@extends('storefront.layout')

@php
    $title = 'About Us | '.config('store.name');
    $description = 'Learn about '.config('store.name').' and our focus on women and men unstitched wear in Pakistan.';
@endphp

@section('content')
    <section class="py-5 text-center">
        <div class="container">
            <div class="small text-uppercase text-secondary fw-bold">Our story</div>
            <h1 class="display-5 fw-normal">About {{ config('store.name') }}</h1>
            <p class="text-secondary mb-0">Unstitched clothing, thoughtfully selected for Pakistani wardrobes.</p>
        </div>
    </section>

    <div class="container pb-5">
        <div class="mx-auto col-lg-8">
            <p class="lead">{{ config('store.name') }} is a focused fashion store for women and men unstitched wear. Our goal is simple: make quality fabrics, contemporary prints and embroidered designs easy to discover and order online across Pakistan.</p>
            <h2 class="h3 mt-5">What we offer</h2>
            <ul class="text-secondary">
                <li>Women unstitched shirts, 2-piece and 3-piece suits.</li>
                <li>Men unstitched fabrics and suit collections.</li>
                <li>Printed, embroidered and seasonal fabric collections.</li>
                <li>Cash on Delivery for convenient nationwide ordering.</li>
            </ul>
            <h2 class="h3 mt-5">Our approach</h2>
            <p class="text-secondary">We keep the catalogue focused rather than mixing unrelated categories. Every collection is presented with clear product photography, pricing, fabric information and product details so customers can make an informed purchase before ordering.</p>
            <h2 class="h3 mt-5">Delivery across Pakistan</h2>
            <p class="text-secondary">Orders are processed through our online store and delivered across Pakistan. Shipping charges and free-delivery eligibility are displayed during checkout.</p>
        </div>
    </div>
@endsection
