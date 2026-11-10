@extends('storefront.layout')

@php
    $storeName = config('store.name');
    $title = 'Golden Cloth House | 30+ Years in Gujrat';
    $description = 'Golden Cloth House has served Gujrat for 30+ years. Explore women’s and men’s unstitched suits at Amin Market, Madina Bazar, Sargodha Road.';
    $storeAddress = config('store.address');
    $schema = json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'AboutPage',
                '@id' => route('about').'#about',
                'name' => $title,
                'description' => $description,
                'url' => route('about'),
                'about' => ['@id' => url('/').'#store'],
                'mainEntity' => ['@id' => url('/').'#store'],
            ],
            [
                '@type' => 'ClothingStore',
                '@id' => url('/').'#store',
                'name' => $storeName,
                'url' => url('/'),
                'description' => $description,
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'Amin Market, Madina Bazar, Sargodha Road',
                    'addressLocality' => 'Gujrat',
                    'addressRegion' => 'Punjab',
                    'addressCountry' => 'PK',
                ],
                'areaServed' => ['Gujrat', 'Pakistan'],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp

@section('content')
    <section class="bg-dark text-white py-5 py-lg-6">
        <div class="container py-lg-4">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-8">
                    <p class="text-uppercase small fw-semibold text-warning mb-3">Serving Gujrat for more than 30 years</p>
                    <h1 class="display-3 fw-normal mb-3">A familiar name in fabric, built on decades of service.</h1>
                    <p class="lead text-white-50 mb-4">
                        {{ $storeName }} is a Gujrat clothing business built around a simple promise: help people find fabric and unstitched suits they feel good wearing, with personal service shaped by decades of experience.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-light btn-lg" href="{{ route('shop') }}">Explore the collection</a>
                        <a class="btn btn-outline-light btn-lg" href="{{ route('contact') }}">Visit or contact us</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="border border-secondary p-4 p-lg-5 h-100">
                        <div class="display-4 fw-semibold text-warning">30+</div>
                        <h2 class="h4">Years of clothing retail experience</h2>
                        <p class="text-white-50 mb-0">
                            A long-standing connection with customers in Gujrat, built through product knowledge, thoughtful selection and service you can speak to in person.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 py-lg-6">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-start">
                <div class="col-lg-4">
                    <p class="small text-uppercase fw-bold text-secondary mb-2">Our history</p>
                    <h2 class="display-6 fw-normal">More than three decades of helping Gujrat dress with confidence.</h2>
                </div>
                <div class="col-lg-8">
                    <p class="lead">
                        For over 30 years, {{ $storeName }} has served people looking for clothing and fabric in Gujrat. Our commitment to dependable products and attentive customer care connects our local roots with a modern online catalogue.
                    </p>
                    <p class="text-secondary">
                        Clothing choices are personal. Fabric has to feel right, a print has to suit the moment, and the details of a suit matter. Our experience comes from listening to customers, understanding what they look for, and helping them compare available options before they decide. We bring that same practical, welcoming approach to every in-store conversation and online product listing.
                    </p>
                    <p class="text-secondary mb-0">
                        We are proud to continue serving our community from Amin Market, Madina Bazar, Sargodha Road, Gujrat. The address is part of our story: a local place to browse, ask questions and discover unstitched clothing, backed by an online shop for customers who prefer to order from home.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-end g-3 mb-4">
                <div class="col-lg-8">
                    <p class="small text-uppercase fw-bold text-secondary mb-2">What you will find</p>
                    <h2 class="display-6 fw-normal mb-0">Unstitched clothing for everyday wear and special moments</h2>
                </div>
                <div class="col-lg-4">
                    <p class="text-secondary mb-0">Explore a focused selection of fabrics and suits for women and men, with product information to help you choose.</p>
                </div>
            </div>
            <div class="row g-3 g-lg-4">
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100 rounded-0 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <span class="text-secondary small text-uppercase fw-bold">01 · Women</span>
                            <h3 class="h4 mt-3">Women’s unstitched suits</h3>
                            <p class="text-secondary mb-0">Browse unstitched 2-piece and 3-piece suits, shirts and coordinated options for everyday dressing, gatherings and seasonal wardrobes.</p>
                        </div>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100 rounded-0 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <span class="text-secondary small text-uppercase fw-bold">02 · Men</span>
                            <h3 class="h4 mt-3">Men’s fabric and suits</h3>
                            <p class="text-secondary mb-0">Find men’s unstitched fabric and suit selections for shalwar kameez, with options suited to different seasons and occasions.</p>
                        </div>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100 rounded-0 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <span class="text-secondary small text-uppercase fw-bold">03 · Fabric</span>
                            <h3 class="h4 mt-3">Seasonal textures</h3>
                            <p class="text-secondary mb-0">Explore fabric choices such as lawn, cotton and khaddar when available, alongside printed and embroidered designs in the current collection.</p>
                        </div>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100 rounded-0 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <span class="text-secondary small text-uppercase fw-bold">04 · Online</span>
                            <h3 class="h4 mt-3">Clear product details</h3>
                            <p class="text-secondary mb-0">Product pages show the available price, images, fabric notes and suit components where those details are provided, so you can shop with clarity.</p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 py-lg-6">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-6">
                    <p class="small text-uppercase fw-bold text-secondary mb-2">How we serve you</p>
                    <h2 class="display-6 fw-normal">Experience that starts with listening</h2>
                    <p class="text-secondary">Our 30-plus years in clothing retail have taught us that helpful service is not about rushing a choice. It is about understanding what a customer needs, sharing clear information and making the available options easy to explore.</p>
                    <p class="text-secondary mb-0">Whether you are visiting our Gujrat shop or browsing online, we aim to make the experience straightforward. Check product photos and details, compare items, then contact us if you need help with a product or order.</p>
                </div>
                <div class="col-lg-6">
                    <div class="border p-4 p-lg-5 h-100">
                        <h3 class="h4">A convenient way to shop fabric in Gujrat and beyond</h3>
                        <p class="text-secondary">Our physical shop welcomes local customers who want to visit Amin Market, Madina Bazar. The online store makes selected women’s and men’s unstitched clothing available to customers across Pakistan.</p>
                        <p class="text-secondary">Place an order through the website and review the delivery information shown during checkout. Cash on Delivery is available where offered by the store. If you have a question before ordering, use the contact page or WhatsApp option when available.</p>
                        <a class="fw-semibold link-dark" href="{{ route('faq') }}">Read ordering and delivery FAQs <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-dark text-white">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <p class="small text-uppercase text-warning fw-bold mb-2">Visit our Gujrat shop</p>
                    <h2 class="display-6 fw-normal">Find us at Amin Market, Madina Bazar</h2>
                    <p class="text-white-50 mb-0">{{ $storeAddress }}</p>
                </div>
                <div class="col-lg-5 d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a class="btn btn-light" href="{{ route('contact') }}">Contact the store</a>
                    <a class="btn btn-outline-light" href="https://www.google.com/maps/search/?api=1&amp;query={{ rawurlencode($storeAddress) }}" target="_blank" rel="noopener noreferrer">Get directions</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 py-lg-6 text-center">
        <div class="container">
            <p class="small text-uppercase fw-bold text-secondary mb-2">Continue your journey</p>
            <h2 class="display-6 fw-normal">Discover the latest from {{ $storeName }}</h2>
            <p class="text-secondary mx-auto mb-4" style="max-width: 42rem;">Explore women’s and men’s unstitched collections, or get in touch with our team for help with a product or order.</p>
            <div class="d-flex justify-content-center flex-wrap gap-2">
                <a class="btn btn-dark btn-lg" href="{{ route('shop') }}">Shop all products</a>
                <a class="btn btn-outline-dark btn-lg" href="{{ route('contact') }}">Contact us</a>
            </div>
        </div>
    </section>
@endsection
