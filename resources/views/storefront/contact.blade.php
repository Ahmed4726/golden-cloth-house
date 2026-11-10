@extends('storefront.layout')

@php
    $storeName = config('store.name');
    $storeAddress = config('store.address');
    $title = 'Contact Golden Cloth House | Gujrat Fabric Shop';
    $description = 'Contact Golden Cloth House in Gujrat about unstitched suits or orders. Visit Amin Market, Madina Bazar, Sargodha Road.';
    $mapUrl = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($storeAddress);
    $schema = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'ContactPage',
        'name' => $title,
        'description' => $description,
        'url' => route('contact'),
        'mainEntity' => [
            '@type' => 'ClothingStore',
            'name' => $storeName,
            'url' => url('/'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Amin Market, Madina Bazar, Sargodha Road',
                'addressLocality' => 'Gujrat',
                'addressRegion' => 'Punjab',
                'addressCountry' => 'PK',
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp

@section('content')
    <section class="bg-light py-5 py-lg-6">
        <div class="container">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <p class="small text-uppercase text-secondary fw-bold mb-2">Visit us in Gujrat · We are here to help</p>
                    <h1 class="display-4 fw-normal">Contact {{ $storeName }}</h1>
                    <p class="lead text-secondary mb-0">Ask us about unstitched suits, fabric availability, an online order or a visit to our shop in Amin Market, Madina Bazar.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a class="btn btn-dark btn-lg" href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer">
                        <i class="fa-solid fa-location-arrow me-2" aria-hidden="true"></i>Get directions
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 py-lg-6">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-7">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <article class="card h-100 border-0 shadow-sm rounded-0">
                                <div class="card-body p-4">
                                    <div class="text-secondary fs-4 mb-3"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></div>
                                    <h2 class="h5">Visit our shop</h2>
                                    <address class="text-secondary mb-3">{{ $storeAddress }}</address>
                                    <a class="link-dark fw-semibold" href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer">Open directions <i class="fa-solid fa-arrow-up-right-from-square ms-1 small" aria-hidden="true"></i></a>
                                </div>
                            </article>
                        </div>
                        <div class="col-md-6">
                            <article class="card h-100 border-0 shadow-sm rounded-0">
                                <div class="card-body p-4">
                                    <div class="text-secondary fs-4 mb-3"><i class="fa-solid fa-comments" aria-hidden="true"></i></div>
                                    <h2 class="h5">Chat about a product</h2>
                                    <p class="text-secondary">Ask about an unstitched suit, fabric details or help with choosing from the current collection.</p>
                                    @if ($whatsappNumber)
                                        <a class="link-success fw-semibold" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}" target="_blank" rel="noopener noreferrer">Message us on WhatsApp <i class="fa-solid fa-arrow-up-right-from-square ms-1 small" aria-hidden="true"></i></a>
                                    @else
                                        <span class="small text-secondary">WhatsApp ordering is currently unavailable.</span>
                                    @endif
                                </div>
                            </article>
                        </div>
                        @if (config('store.contact_phone'))
                            <div class="col-md-6">
                                <article class="card h-100 border-0 shadow-sm rounded-0">
                                    <div class="card-body p-4">
                                        <div class="text-secondary fs-4 mb-3"><i class="fa-solid fa-phone" aria-hidden="true"></i></div>
                                        <h2 class="h5">Call the store</h2>
                                        <p class="text-secondary">For product or order enquiries, call us at:</p>
                                        <a class="link-dark fw-semibold" href="tel:{{ config('store.contact_phone') }}">{{ config('store.contact_phone') }}</a>
                                    </div>
                                </article>
                            </div>
                        @endif
                        @if (config('store.contact_email'))
                            <div class="col-md-6">
                                <article class="card h-100 border-0 shadow-sm rounded-0">
                                    <div class="card-body p-4">
                                        <div class="text-secondary fs-4 mb-3"><i class="fa-solid fa-envelope" aria-hidden="true"></i></div>
                                        <h2 class="h5">Email us</h2>
                                        <p class="text-secondary">Send us your general or order-related questions at:</p>
                                        <a class="link-dark fw-semibold text-break" href="mailto:{{ config('store.contact_email') }}">{{ config('store.contact_email') }}</a>
                                    </div>
                                </article>
                            </div>
                        @endif
                    </div>
                </div>
                <aside class="col-lg-5">
                    <div class="bg-dark text-white p-4 p-lg-5 h-100">
                        <p class="small text-uppercase text-warning fw-bold">Customer care</p>
                        <h2 class="h3">How can we help?</h2>
                        <p class="text-white-50">We can help with questions about the products shown online, order tracking and delivery. For the quickest assistance with an existing order, please share the details below.</p>
                        <hr class="border-secondary">
                        <h3 class="h6 text-uppercase">For an order enquiry, have ready</h3>
                        <ul class="text-white-50 ps-3">
                            <li class="mb-2">Your order number, if available</li>
                            <li class="mb-2">The phone number used when placing the order</li>
                            <li>The product name or link you are asking about</li>
                        </ul>
                        <div class="d-grid gap-2 mt-4">
                            <a class="btn btn-light" href="{{ route('order.track') }}">Track your order</a>
                            <a class="btn btn-outline-light" href="{{ route('faq') }}">Read frequently asked questions</a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <h2 class="h3">Looking for a fabric shop in Gujrat?</h2>
                    <p class="text-secondary mb-0">{{ $storeName }} is located at Amin Market, Madina Bazar, Sargodha Road, Gujrat. Visit us to explore the current selection of women’s and men’s unstitched clothing, or browse available products online before you contact us.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a class="btn btn-dark" href="{{ route('shop') }}">Browse unstitched suits</a>
                </div>
            </div>
        </div>
    </section>
@endsection
