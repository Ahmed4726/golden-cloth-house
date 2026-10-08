@extends('storefront.layout')

@php
    $title = 'Privacy Policy | '.config('store.name');
@endphp

@section('content')
    <section class="py-5 text-center">
        <div class="container">
            <div class="small text-uppercase text-secondary fw-bold">Legal</div>
            <h1 class="display-5 fw-normal">Privacy Policy</h1>
            <p class="text-secondary mb-0">Golden Cloth House uses this website and its social publishing tools to manage products and authorized social accounts.</p>
        </div>
    </section>
    <section class="container pb-5">
        <div class="mx-auto col-lg-8">
            <h2 class="h3 mt-4">Information we process</h2>
            <p class="text-secondary">We may process administrator account details, product information, uploaded product media, and social-account authorization tokens needed to publish content at your request.</p>
            <h2 class="h3 mt-4">Social accounts</h2>
            <p class="text-secondary">Social access tokens are stored encrypted by the application and are used only for the connected publishing features. We do not sell social-account data.</p>
            <h2 class="h3 mt-4">Contact</h2>
            <p class="text-secondary">For privacy questions, contact the Golden Cloth House administrator using the contact details shown on this website.</p>
        </div>
    </section>
@endsection
