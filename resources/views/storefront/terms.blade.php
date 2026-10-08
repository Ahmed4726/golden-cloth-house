@extends('storefront.layout')

@php
    $title = 'Terms of Use | '.config('store.name');
@endphp

@section('content')
    <section class="py-5 text-center">
        <div class="container">
            <div class="small text-uppercase text-secondary fw-bold">Legal</div>
            <h1 class="display-5 fw-normal">Terms of Use</h1>
            <p class="text-secondary mb-0">Use of Golden Cloth House services is subject to these terms.</p>
        </div>
    </section>
    <section class="container pb-5">
        <div class="mx-auto col-lg-8">
            <h2 class="h3 mt-4">Website</h2>
            <p class="text-secondary">Product information, prices and availability may change without notice. Product images and descriptions are provided for shopping and publishing purposes.</p>
            <h2 class="h3 mt-4">Social publishing</h2>
            <p class="text-secondary">Only administrators who have authorized a social account may publish through the application. The account owner remains responsible for complying with each platform's terms and policies.</p>
            <h2 class="h3 mt-4">Contact</h2>
            <p class="text-secondary">Questions about these terms can be directed to the Golden Cloth House administrator.</p>
        </div>
    </section>
@endsection
