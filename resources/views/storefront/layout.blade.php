<!doctype html>
<html lang="en-PK">
<head>
    @php
        $siteName = config('store.name');
        $pageTitle = $title ?? $siteName;
        $pageDescription = $description ?? 'Golden Cloth House — خواتین اور مردوں کے معیاری ان سٹچڈ ملبوسات، پاکستان بھر میں Cash on Delivery کے ساتھ۔';
        $filteredShopPage = request()->routeIs('shop') && request()->query->count() > 0;
        $canonical = $canonical ?? ($filteredShopPage ? route('shop') : url()->current());
        $shareImage = $shareImage ?? null;
        $cartCount = collect(session('cart', []))->sum('quantity');
        $indexable = ! $filteredShopPage && ! request()->routeIs('cart.*', 'checkout*', 'order.track*', 'login', 'register');
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="{{ $indexable ? 'index,follow,max-image-preview:large' : 'noindex,follow' }}">
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&family=Noto+Nastaliq+Urdu:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .ratio-2x1 { --bs-aspect-ratio: 50%; }
        .ratio-3x4 { --bs-aspect-ratio: 133.333333%; }
        .ratio-3x1 { --bs-aspect-ratio: 33.333333%; }
        .ratio-4x5 { --bs-aspect-ratio: 125%; }
        .ratio-7x5 { --bs-aspect-ratio: 71.428571%; }
        .quick-view-button { width: 2.75rem; height: 2.75rem; padding: 0; }
        .product-card-indicators,
        .product-detail-indicators,
        .quick-view-indicators { position: static; justify-content: center; gap: .4rem; margin: .55rem 0 0; }
        .product-card-indicators [data-bs-target],
        .product-detail-indicators [data-bs-target],
        .quick-view-indicators [data-bs-target] { flex: 0 0 auto; width: .55rem; height: .55rem; margin: 0; border: 0; border-radius: 50%; background-color: #adb5bd; opacity: 1; }
        .product-card-indicators .active,
        .product-detail-indicators .active,
        .quick-view-indicators .active { background-color: #212529; opacity: 1; }
        .product-card-media,
        .product-detail-gallery-wrap,
        .quick-view-gallery-wrap { min-width: 0; }
        .product-card-gallery .carousel-inner,
        .product-card-gallery .carousel-item { height: 100%; }
        .product-detail-gallery .carousel-control-prev,
        .product-detail-gallery .carousel-control-next,
        .quick-view-gallery .carousel-control-prev,
        .quick-view-gallery .carousel-control-next { top: 50%; bottom: auto; width: 2.75rem; height: 2.75rem; border-radius: 50%; background-color: rgba(0, 0, 0, .45); opacity: 1; transform: translateY(-50%); }
        .product-detail-gallery .carousel-control-prev,
        .quick-view-gallery .carousel-control-prev { right: auto; left: .75rem; }
        .product-detail-gallery .carousel-control-next,
        .quick-view-gallery .carousel-control-next { right: .75rem; left: auto; }
        .product-detail-thumbnails { display: flex; gap: .5rem; margin-top: .75rem; overflow-x: auto; padding: .15rem .1rem .35rem; }
        .product-detail-thumbnails button { flex: 0 0 5rem; aspect-ratio: 1; overflow: hidden; padding: .15rem; border: 2px solid transparent; border-radius: .35rem; background: #fff; }
        .product-detail-thumbnails button.active { border-color: #b08b43; }
        .product-detail-thumbnails img { display: block; width: 100%; height: 100%; object-fit: contain; }
        .quick-view-gallery .carousel-item img { height: 100%; }
        .store-add-to-cart { min-height: 3rem; display: inline-flex; align-items: center; justify-content: center; gap: .5rem; }
        .product-card-cart-form { min-width: 0; }
        .product-card-cart-button { min-width: 0; padding-inline: .5rem; white-space: nowrap; }
        .product-card-whatsapp-button { width: 3rem; min-width: 3rem; min-height: 3rem; display: inline-flex; align-items: center; justify-content: center; font-size: 1.15rem; }
        .quick-view-cart-form { display: grid; grid-template-columns: minmax(5rem, .35fr) minmax(0, 1fr); align-items: end; gap: .75rem; }
        .quick-view-cart-form .form-control { min-height: 3rem; }
        .quick-view-cart-form .store-add-to-cart { width: 100%; }
        .storefront-nav .navbar-nav > .nav-item > .nav-link { position: relative; padding-block: .9rem; transition: color .2s ease; }
        .storefront-nav .navbar-nav > .nav-item > .nav-link::after { position: absolute; right: .5rem; bottom: .45rem; left: .5rem; height: 2px; background: #d3b16f; content: ""; transform: scaleX(0); transform-origin: left; transition: transform .2s ease; }
        .storefront-nav .navbar-nav > .nav-item:hover > .nav-link::after,
        .storefront-nav .navbar-nav > .nav-item:focus-within > .nav-link::after { transform: scaleX(1); }
        .storefront-nav .store-nav-dropdown { min-width: 16rem; margin-top: .15rem; padding: .65rem; border: 1px solid rgba(0, 0, 0, .08); border-top: 2px solid #d3b16f; border-radius: .35rem; box-shadow: 0 1rem 2rem rgba(0, 0, 0, .18); }
        .storefront-nav .store-nav-dropdown .dropdown-header { padding: .55rem .75rem .4rem; color: #777; font-size: .68rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        .storefront-nav .store-nav-dropdown .dropdown-item { padding: .62rem .75rem; border-radius: .25rem; font-size: .92rem; transition: background-color .15s ease, color .15s ease; }
        .storefront-nav .store-nav-dropdown .dropdown-item:hover,
        .storefront-nav .store-nav-dropdown .dropdown-item:focus { color: #171717; background: #f5f0e5; }
        .storefront-nav .dropdown-divider { margin: .4rem .5rem; }
        .storefront-mobile-menu .accordion-item { border-color: #343a40; }
        .storefront-mobile-menu .accordion-button { min-height: 3.25rem; font-weight: 600; }
        .storefront-mobile-menu .accordion-button:not(.collapsed) { color: #e6ca8b; box-shadow: none; }
        .storefront-mobile-menu .accordion-button:focus { box-shadow: inset 0 0 0 2px rgba(211, 177, 111, .55); }
        .storefront-mobile-menu .store-mobile-link { transition: color .15s ease, background-color .15s ease; }
        .storefront-mobile-menu .store-mobile-link:hover,
        .storefront-mobile-menu .store-mobile-link:focus { color: #e6ca8b !important; background: #252a2e; }
        @media (min-width: 992px) {
            .py-lg-6 { padding-block: 5rem !important; }
            .storefront-nav .nav-item.dropdown:hover > .store-nav-dropdown,
            .storefront-nav .nav-item.dropdown:focus-within > .store-nav-dropdown { display: block; }
        }

        #homeHero .carousel-item .ratio {
            --bs-aspect-ratio: 42.857143%;
        }

        @media (min-width: 992px) {
            #homeHero .carousel-item .ratio {
                --bs-aspect-ratio: 34.375%;
            }
        }
    </style>
    @stack('styles')

    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:locale" content="en_PK">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if ($shareImage)
        <meta property="og:image" content="{{ $shareImage }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    @if ($shareImage)
        <meta name="twitter:image" content="{{ $shareImage }}">
    @endif

    <title>{{ $pageTitle }}</title>

    @if (isset($schema))
        <script type="application/ld+json">{!! $schema !!}</script>
    @endif
</head>
<body class="bg-white text-dark">
    <a class="visually-hidden-focusable position-absolute top-0 start-0 p-2 bg-white text-dark z-3" href="#main-content">Skip to content</a>
    <header class="sticky-top">
        <div class="bg-black text-white border-bottom border-secondary">
            <div class="container-fluid px-3 px-lg-4 py-2">
                <div class="row align-items-center g-2">
                    <div class="col-4 d-flex align-items-center gap-3">
                        <button class="btn btn-link text-white p-2 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#gchMobileMenu" aria-controls="gchMobileMenu" aria-label="Open menu">
                            <i class="fa-solid fa-bars fs-5" aria-hidden="true"></i>
                        </button>
                        @if (config('store.contact_phone'))
                            <a class="text-white fs-5 d-none d-lg-inline-block" href="tel:{{ config('store.contact_phone') }}" aria-label="Call Golden Cloth House">
                                <i class="fa-solid fa-phone"></i>
                            </a>
                        @endif
                        @if ($whatsappNumber)
                            <a class="text-success fs-4 d-none d-lg-inline-block" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Golden Cloth House">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                        @endif
                    </div>

                    <div class="col-4 text-center">
                        <a class="d-inline-block" href="{{ route('home') }}" aria-label="{{ $siteName }} home">
                            <img class="img-fluid" src="{{ asset('images/golden-cloth-house-logo-header.svg') }}" width="170" height="42" alt="{{ $siteName }}">
                        </a>
                    </div>

                    <div class="col-4 d-flex align-items-center justify-content-end gap-3">
                        <form class="d-none d-md-flex input-group input-group-sm w-auto" method="get" action="{{ route('shop') }}">
                            <input class="form-control bg-black text-white border-secondary" type="search" name="q" value="{{ request('q') }}" placeholder="Search" aria-label="Search products">
                            <button class="btn btn-outline-light" type="submit" aria-label="Search">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </form>
                        <button class="btn btn-link text-white p-2 d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#gchMobileSearch" aria-expanded="false" aria-controls="gchMobileSearch" aria-label="Open search">
                            <i class="fa-solid fa-magnifying-glass fs-5" aria-hidden="true"></i>
                        </button>
                        <a class="text-white fs-5 d-none d-sm-inline-block" href="{{ route('login') }}" aria-label="Account">
                            <i class="fa-solid fa-user"></i>
                        </a>
                        <a class="text-white fs-5 position-relative" href="{{ route('cart.index') }}" aria-label="Shopping bag">
                            <i class="fa-solid fa-bag-shopping"></i>
                            @if ($cartCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-warning">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="collapse d-md-none bg-black border-bottom border-secondary" id="gchMobileSearch">
            <form class="container-fluid px-3 pb-3" method="get" action="{{ route('shop') }}">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Search products" aria-label="Search products">
                    <button class="btn btn-light" type="submit" aria-label="Submit search">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        </div>

        <nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-secondary storefront-nav" aria-label="Primary navigation">
            <div class="container-fluid px-3 px-lg-4">
                <div class="collapse navbar-collapse justify-content-center d-none d-lg-flex">
                    <ul class="navbar-nav align-items-center gap-lg-4">
                        <li class="nav-item">
                            <a class="nav-link text-uppercase fw-medium" href="{{ route('shop', ['sort' => 'newest']) }}">New In</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link text-uppercase fw-medium dropdown-toggle" href="{{ route('shop', ['category' => 'women-unstitched']) }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Women</a>
                            <ul class="dropdown-menu store-nav-dropdown">
                                <li><span class="dropdown-header">Women’s collections</span></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['category' => 'women-unstitched']) }}">All Women Unstitched</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => '2 Piece', 'category' => 'women-unstitched']) }}">2 Piece Suits</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => '3 Piece', 'category' => 'women-unstitched']) }}">3 Piece Suits</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Embroidered', 'category' => 'women-unstitched']) }}">Embroidered</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Lawn', 'category' => 'women-unstitched']) }}">Lawn</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'women-unstitched']) }}">Khaddar</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Slub', 'category' => 'women-unstitched']) }}">Slub</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link text-uppercase fw-medium dropdown-toggle" href="{{ route('shop', ['category' => 'men-unstitched']) }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Men</a>
                            <ul class="dropdown-menu store-nav-dropdown">
                                <li><span class="dropdown-header">Men’s collections</span></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['category' => 'men-unstitched']) }}">All Men Unstitched</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Suit', 'category' => 'men-unstitched']) }}">Unstitched Suits</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Cotton', 'category' => 'men-unstitched']) }}">Cotton</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'men-unstitched']) }}">Khaddar</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase fw-medium text-danger" href="{{ route('shop', ['sort' => 'price_asc']) }}">Sale</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase fw-medium" href="{{ route('about') }}">Our story</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase fw-medium" href="{{ route('contact') }}">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="gchMobileMenu" aria-labelledby="gchMobileMenuLabel">
            <div class="offcanvas-header border-bottom border-secondary">
                <a href="{{ route('home') }}" class="d-inline-block">
                    <img class="img-fluid" src="{{ asset('images/golden-cloth-house-logo-header.svg') }}" width="186" height="45" alt="گولڈن کلاتھ ہاؤس">
                </a>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                <div class="accordion accordion-flush storefront-mobile-menu" id="gchMobileAccordion">
                    <div class="accordion-item bg-dark text-white">
                        <h2 class="accordion-header">
                            <a class="accordion-button collapsed bg-dark text-white text-decoration-none" href="{{ route('shop', ['sort' => 'newest']) }}">New In</a>
                        </h2>
                    </div>
                    <div class="accordion-item bg-dark text-white">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-dark text-white" type="button" data-bs-toggle="collapse" data-bs-target="#womenMenu">Women</button>
                        </h2>
                        <div id="womenMenu" class="accordion-collapse collapse" data-bs-parent="#gchMobileAccordion">
                            <div class="accordion-body p-0">
                                <a class="store-mobile-link d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['category' => 'women-unstitched']) }}">All Women Unstitched</a>
                                <a class="store-mobile-link d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => '2 Piece', 'category' => 'women-unstitched']) }}">2 Piece Suits</a>
                                <a class="store-mobile-link d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => '3 Piece', 'category' => 'women-unstitched']) }}">3 Piece Suits</a>
                                <a class="store-mobile-link d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Embroidered', 'category' => 'women-unstitched']) }}">Embroidered</a>
                                <a class="store-mobile-link d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Lawn', 'category' => 'women-unstitched']) }}">Lawn</a>
                                <a class="store-mobile-link d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'women-unstitched']) }}">Khaddar</a>
                                <a class="store-mobile-link d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Slub', 'category' => 'women-unstitched']) }}">Slub</a>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-dark text-white">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-dark text-white" type="button" data-bs-toggle="collapse" data-bs-target="#menMenu">Men</button>
                        </h2>
                        <div id="menMenu" class="accordion-collapse collapse" data-bs-parent="#gchMobileAccordion">
                            <div class="accordion-body p-0">
                                <a class="store-mobile-link d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['category' => 'men-unstitched']) }}">All Men Unstitched</a>
                                <a class="store-mobile-link d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Suit', 'category' => 'men-unstitched']) }}">Unstitched Suits</a>
                                <a class="store-mobile-link d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Cotton', 'category' => 'men-unstitched']) }}">Cotton</a>
                                <a class="store-mobile-link d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'men-unstitched']) }}">Khaddar</a>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-dark text-white">
                        <h2 class="accordion-header">
                            <a class="accordion-button bg-dark text-danger text-decoration-none" href="{{ route('shop', ['sort' => 'price_asc']) }}">Sale</a>
                        </h2>
                    </div>
                    <div class="accordion-item bg-dark text-white">
                        <h2 class="accordion-header">
                            <a class="accordion-button bg-dark text-white text-decoration-none" href="{{ route('about') }}">Our story</a>
                        </h2>
                    </div>
                    <div class="accordion-item bg-dark text-white">
                        <h2 class="accordion-header">
                            <a class="accordion-button bg-dark text-white text-decoration-none" href="{{ route('contact') }}">Contact</a>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main id="main-content">
        @if (session('success'))
            <div class="container py-3">
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="container py-3">
                <div class="alert alert-danger mb-0">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <section class="bg-light py-5">
        <div class="container text-center">
            <h2 class="h3 fw-normal">Stay connected with {{ $siteName }}</h2>
            <p class="text-secondary mb-4">New arrivals, seasonal collections and offers.</p>
            <div class="d-flex justify-content-center gap-2">
                @if (config('store.instagram'))
                    <a class="btn btn-outline-dark" href="{{ config('store.instagram') }}" target="_blank" rel="noopener"><i class="fa-brands fa-instagram me-1"></i>Instagram</a>
                @endif
                @if (config('store.facebook'))
                    <a class="btn btn-outline-dark" href="{{ config('store.facebook') }}" target="_blank" rel="noopener"><i class="fa-brands fa-facebook me-1"></i>Facebook</a>
                @endif
            </div>
        </div>
    </section>

    <footer class="bg-dark text-light pt-5 pb-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h2 class="h4 fw-normal">{{ $siteName }}</h2>
                    <p class="text-secondary">Women and men unstitched wear, selected for Pakistani wardrobes.</p>
                    @if (config('store.address'))
                        <div class="small text-secondary"><i class="fa-solid fa-location-dot me-2"></i>{{ config('store.address') }}</div>
                    @endif
                </div>
                <div class="col-6 col-lg-2">
                    <h3 class="h6 text-uppercase">Shop</h3>
                    <div class="d-grid gap-2">
                        <a class="link-light link-opacity-75-hover text-decoration-none" href="{{ route('shop', ['sort' => 'newest']) }}">New In</a>
                        <a class="link-light link-opacity-75-hover text-decoration-none" href="{{ route('shop', ['category' => 'women-unstitched']) }}">Women</a>
                        <a class="link-light link-opacity-75-hover text-decoration-none" href="{{ route('shop', ['category' => 'men-unstitched']) }}">Men</a>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <h3 class="h6 text-uppercase">Help</h3>
                    <div class="d-grid gap-2">
                        <a class="link-light link-opacity-75-hover text-decoration-none" href="{{ route('faq') }}">FAQ</a>
                        <a class="link-light link-opacity-75-hover text-decoration-none" href="{{ route('contact') }}">Contact</a>
                        <a class="link-light link-opacity-75-hover text-decoration-none" href="{{ route('order.track') }}">Track Order</a>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <h3 class="h6 text-uppercase">Customer Care</h3>
                    @if (config('store.contact_phone'))
                        <div class="mb-2"><i class="fa-solid fa-phone me-2"></i>{{ config('store.contact_phone') }}</div>
                    @endif
                    @if (config('store.contact_email'))
                        <div class="mb-2"><i class="fa-solid fa-envelope me-2"></i>{{ config('store.contact_email') }}</div>
                    @endif
                    @if ($whatsappNumber)
                        <a class="link-success text-decoration-none" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp me-2"></i>WhatsApp</a>
                    @endif
                </div>
            </div>

            <hr class="border-secondary my-4">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 small text-secondary">
                <span>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</span>
                <div class="d-flex gap-3">
                    <a class="link-secondary text-decoration-none" href="{{ route('privacy') }}">Privacy</a>
                    <a class="link-secondary text-decoration-none" href="{{ route('terms') }}">Terms</a>
                    <a class="link-secondary text-decoration-none" href="{{ route('about') }}">About</a>
                </div>
            </div>
        </div>
    </footer>

    @if ($whatsappNumber)
        <a class="btn btn-success rounded-circle position-fixed bottom-0 end-0 m-3 shadow" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Golden Cloth House">
            <i class="fa-brands fa-whatsapp fs-5"></i>
        </a>
    @endif

    <div class="modal fade" id="productQuickView" data-whatsapp-number="{{ $whatsappNumber ? preg_replace('/[^0-9]/', '', $whatsappNumber) : '' }}" tabindex="-1" aria-labelledby="productQuickViewTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-0 border-0">
                <div class="modal-header border-0">
                    <h2 class="modal-title h5" id="productQuickViewTitle">Product details</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6">
                            <div class="quick-view-gallery-wrap">
                                <div
                                    id="quickViewGallery"
                                    class="carousel slide quick-view-gallery ratio ratio-7x5 bg-light"
                                    data-bs-interval="false"
                                    data-bs-touch="true"
                                    aria-label="Product images"
                                >
                                    <div id="quickViewGalleryItems" class="carousel-inner h-100">
                                        <div class="carousel-item h-100 active">
                                            <img id="quickViewImage" class="d-block w-100 h-100 object-fit-contain" src="{{ asset('images/product-placeholder.svg') }}" alt="" loading="lazy">
                                        </div>
                                    </div>
                                    <button class="carousel-control-prev d-none" type="button" data-bs-target="#quickViewGallery" data-bs-slide="prev" aria-label="Previous product image">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    </button>
                                    <button class="carousel-control-next d-none" type="button" data-bs-target="#quickViewGallery" data-bs-slide="next" aria-label="Next product image">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    </button>
                                </div>
                                <div id="quickViewGalleryIndicators" class="carousel-indicators quick-view-indicators d-none"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="quickViewCategory" class="small text-uppercase text-secondary mb-2"></div>
                            <div id="quickViewPrice" class="h5 fw-bold mb-1"></div>
                            <div id="quickViewComparePrice" class="small text-secondary text-decoration-line-through mb-3 d-none"></div>
                            <p id="quickViewDescription" class="text-secondary"></p>
                            <div id="quickViewSuitDetails" class="small mb-3 d-none">
                                <div id="quickViewPieceCount" class="fw-semibold mb-1"></div>
                                <ul id="quickViewPieceList" class="list-unstyled mb-0"></ul>
                            </div>
                            <div id="quickViewStock" class="small mb-4"></div>
                            <div class="d-grid gap-2">
                                <form id="quickViewCartForm" class="quick-view-cart-form" method="POST" action="#">
                                    @csrf
                                    <div>
                                        <label class="form-label small fw-semibold mb-1" for="quickViewQuantity">Quantity</label>
                                        <input id="quickViewQuantity" class="form-control" type="number" name="quantity" value="1" min="1" max="20" required>
                                    </div>
                                    <button id="quickViewAddButton" class="btn btn-dark store-add-to-cart fw-semibold" type="submit"><i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>Add to cart</button>
                                </form>
                                <a id="quickViewLink" class="btn btn-outline-dark w-100" href="#">View product</a>
                            </div>
                            <a id="quickViewWhatsAppLink" class="btn btn-success w-100 mt-2 d-none" href="#" target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands fa-whatsapp me-2" aria-hidden="true"></i>Order via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.product-card-gallery, .product-detail-gallery, .quick-view-gallery').forEach((carousel) => {
            carousel.addEventListener('slide.bs.carousel', (event) => {
                const target = `#${carousel.id}`;
                const indicators = document.querySelectorAll(
                    `[data-bs-target="${target}"][data-bs-slide-to]`
                );

                indicators.forEach((indicator) => {
                    const isActive = Number(indicator.dataset.bsSlideTo) === event.to;
                    indicator.classList.toggle('active', isActive);
                    indicator.setAttribute('aria-current', isActive ? 'true' : 'false');
                });
            });
        });

        document.getElementById('productQuickView')?.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            const modal = event.currentTarget;
            const inStock = trigger.dataset.productInStock === 'true';
            const description = trigger.dataset.productDescription;
            const comparePrice = trigger.dataset.productComparePrice;
            const whatsappLink = modal.querySelector('#quickViewWhatsAppLink');
            const quantityInput = modal.querySelector('#quickViewQuantity');
            const quickViewGallery = modal.querySelector('#quickViewGallery');
            const galleryItems = modal.querySelector('#quickViewGalleryItems');
            const galleryIndicators = modal.querySelector('#quickViewGalleryIndicators');
            const galleryControls = quickViewGallery.querySelectorAll('.carousel-control-prev, .carousel-control-next');
            const productImages = JSON.parse(trigger.dataset.productImages || '[]');
            const galleryImages = productImages.length ? productImages : [trigger.dataset.productImage];
            const includedPieces = trigger.dataset.productIncludedPieces
                ? trigger.dataset.productIncludedPieces.split(',')
                : [];
            const pieceLabels = {
                kameez: 'Kameez (shirt)',
                dupatta: 'Dupatta',
                trouser: 'Trouser / Shalwar',
            };
            const pieceLengths = {
                kameez: trigger.dataset.productPieceLengthKameez,
                dupatta: trigger.dataset.productPieceLengthDupatta,
                trouser: trigger.dataset.productPieceLengthTrouser,
            };
            const suitDetails = modal.querySelector('#quickViewSuitDetails');
            const pieceCount = Number(trigger.dataset.productPieceCount) || includedPieces.length;
            const pieceList = modal.querySelector('#quickViewPieceList');

            modal.querySelector('#productQuickViewTitle').textContent = trigger.dataset.productName;
            galleryItems.replaceChildren();
            galleryIndicators.replaceChildren();
            galleryImages.forEach((image, index) => {
                const item = document.createElement('div');
                item.className = `carousel-item h-100${index === 0 ? ' active' : ''}`;

                const imageElement = document.createElement('img');
                imageElement.className = 'd-block w-100 h-100 object-fit-contain';
                imageElement.src = image;
                imageElement.alt = `${trigger.dataset.productName} image ${index + 1}`;
                imageElement.loading = index === 0 ? 'eager' : 'lazy';
                item.appendChild(imageElement);
                galleryItems.appendChild(item);

                if (galleryImages.length > 1) {
                    const indicator = document.createElement('button');
                    indicator.type = 'button';
                    indicator.dataset.bsTarget = '#quickViewGallery';
                    indicator.dataset.bsSlideTo = String(index);
                    indicator.classList.toggle('active', index === 0);
                    indicator.setAttribute('aria-current', index === 0 ? 'true' : 'false');
                    indicator.setAttribute('aria-label', `Show product image ${index + 1}`);
                    galleryIndicators.appendChild(indicator);
                }
            });
            galleryIndicators.classList.toggle('d-none', galleryImages.length < 2);
            galleryControls.forEach((control) => {
                control.classList.toggle('d-none', galleryImages.length < 2);
            });
            bootstrap.Carousel.getOrCreateInstance(quickViewGallery, {
                interval: false,
                touch: true,
            }).to(0);
            modal.querySelector('#quickViewCategory').textContent = trigger.dataset.productCategory;
            modal.querySelector('#quickViewPrice').textContent = trigger.dataset.productPrice;
            modal.querySelector('#quickViewPrice').classList.toggle('d-none', !trigger.dataset.productPrice);
            modal.querySelector('#quickViewComparePrice').textContent = comparePrice;
            modal.querySelector('#quickViewComparePrice').classList.toggle('d-none', !comparePrice);
            modal.querySelector('#quickViewDescription').textContent = description;
            modal.querySelector('#quickViewDescription').classList.toggle('d-none', !description);
            modal.querySelector('#quickViewStock').textContent = inStock ? 'In stock' : 'Out of stock';
            modal.querySelector('#quickViewStock').className = `small mb-4 ${inStock ? 'text-success' : 'text-secondary'}`;
            modal.querySelector('#quickViewLink').href = trigger.dataset.productUrl;
            modal.querySelector('#quickViewCartForm').action = trigger.dataset.cartUrl;
            modal.querySelector('#quickViewAddButton').disabled = !inStock;
            quantityInput.max = String(Math.min(Number(trigger.dataset.productStock) || 1, 20));
            quantityInput.value = '1';
            quantityInput.disabled = !inStock;
            modal.querySelector('#quickViewPieceCount').textContent = pieceCount
                ? `Total pieces: ${pieceCount}`
                : '';
            pieceList.replaceChildren();
            includedPieces.forEach((piece) => {
                const item = document.createElement('li');
                const length = pieceLengths[piece];
                item.textContent = `${pieceLabels[piece] || piece}${length ? ` — ${length} in` : ''}`;
                pieceList.appendChild(item);
            });
            suitDetails.classList.toggle('d-none', !pieceCount && includedPieces.length === 0);

            const updateWhatsAppOrderLink = () => {
                const number = modal.dataset.whatsappNumber;
                whatsappLink.classList.toggle('d-none', !number || !inStock);

                if (!number || !inStock) return;

                const pieces = includedPieces.map((piece) => {
                    const length = pieceLengths[piece];
                    return `${pieceLabels[piece] || piece}${length ? ` (${length} in)` : ''}`;
                });
                const requestedQuantity = Number(quantityInput.value);
                const quantity = Number.isInteger(requestedQuantity) && requestedQuantity > 0
                    ? Math.min(requestedQuantity, Number(quantityInput.max))
                    : 1;
                const message = [
                    'Hello, I would like to order this product:',
                    trigger.dataset.productName,
                    `Quantity: ${quantity}`,
                    `Unit price: ${trigger.dataset.productPrice}`,
                    ...(pieceCount ? [`Total pieces: ${pieceCount}`] : []),
                    ...(pieces.length ? [`Included pieces: ${pieces.join(', ')}`] : []),
                    `Product link: ${trigger.dataset.productUrl}`,
                ].join('\n');

                whatsappLink.href = `https://wa.me/${number}?text=${encodeURIComponent(message)}`;
            };

            quantityInput.oninput = updateWhatsAppOrderLink;
            quantityInput.onchange = updateWhatsAppOrderLink;
            updateWhatsAppOrderLink();
        });
    </script>
    @stack('scripts')
</body>
</html>
