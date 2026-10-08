<!doctype html>
<html lang="ur">
<head>
    @php
        $siteName = config('store.name');
        $pageTitle = $title ?? $siteName;
        $pageDescription = $description ?? 'Golden Cloth House — خواتین اور مردوں کے معیاری ان سٹچڈ ملبوسات، پاکستان بھر میں Cash on Delivery کے ساتھ۔';
        $canonical = $canonical ?? url()->current();
        $shareImage = $shareImage ?? null;
        $cartCount = collect(session('cart', []))->sum('quantity');
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&family=Noto+Nastaliq+Urdu:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
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
    <link rel="stylesheet" href="{{ asset('css/storefront.css') }}">

    <style>
        .gch-bootstrap-header {
            position: sticky;
            top: 0;
            z-index: 1050;
            background: #000 !important;
            color: #fff;
            border: 0 !important;
        }

        .gch-header-main {
            background: #000;
            border-bottom: 1px solid rgba(255,255,255,.75);
        }

        .gch-header-row {
            min-height: 58px;
        }

        .gch-logo {
            position: absolute;
            left: 50%;
            top: 29px;
            transform: translate(-50%, -50%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .gch-logo img {
            display: block;
            width: 225px;
            height: auto;
            max-height: 42px;
            object-fit: contain;
        }

        .gch-contact,
        .gch-header-main .d-flex:last-child {
            min-width: 190px;
        }

        .gch-header-main .d-flex:last-child {
            justify-content: flex-end;
        }

        .gch-header-icon,
        .gch-whatsapp {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff !important;
            font-size: 22px;
            text-decoration: none;
        }

        .gch-whatsapp {
            color: #25d366 !important;
            font-size: 27px;
        }

        .gch-header-icon:hover,
        .gch-whatsapp:hover {
            opacity: .8;
        }

        .gch-search {
            width: 220px;
            border-bottom: 1px solid rgba(255,255,255,.8);
        }

        .gch-search input {
            width: calc(100% - 34px);
            border: 0;
            outline: 0;
            background: transparent;
            color: #fff;
            padding: 5px 0;
        }

        .gch-search input::placeholder {
            color: rgba(255,255,255,.85);
        }

        .gch-search button {
            width: 30px;
            border: 0;
            background: transparent;
            color: #fff;
            padding: 0;
        }

        .gch-bag {
            position: relative;
        }

        .gch-bag .cart-count {
            position: absolute;
            top: -4px;
            right: -5px;
            background: #d8b24c;
            color: #000;
        }

        .gch-nav {
            min-height: 58px;
            padding: 0 !important;
            background: #000 !important;
            border: 0 !important;
        }

        .gch-nav .container-fluid {
            min-height: 58px;
        }

        .gch-nav .navbar-nav {
            min-height: 58px;
        }

        .gch-nav-link {
            color: #fff !important;
            font-size: 16px !important;
            font-weight: 500 !important;
            letter-spacing: .01em !important;
            padding: 18px 18px !important;
            text-transform: uppercase;
        }

        .gch-nav-link:hover,
        .gch-nav-link:focus {
            color: #ddd !important;
        }

        .gch-sale {
            color: #ff3030 !important;
        }

        .gch-dropdown {
            min-width: 240px;
            border-radius: 0;
            border: 0;
            margin-top: 0;
            padding: 8px 0;
            box-shadow: 0 12px 30px rgba(0,0,0,.18);
        }

        .gch-dropdown .dropdown-item {
            padding: 9px 18px;
            font-size: 14px;
        }

        .gch-toggler {
            margin-left: 0;
            width: 42px;
            height: 42px;
        }

        .gch-toggler .navbar-toggler-icon {
            width: 25px;
            height: 25px;
        }

        .gch-offcanvas {
            width: min(88vw, 380px) !important;
            background: #000 !important;
            color: #fff;
        }

        .gch-offcanvas .offcanvas-header {
            min-height: 72px;
            border-color: rgba(255,255,255,.2) !important;
        }

        .gch-mobile-logo img {
            width: 190px;
            max-height: 40px;
            object-fit: contain;
        }

        .gch-mobile-accordion,
        .gch-mobile-accordion .accordion-item,
        .gch-mobile-accordion .accordion-button,
        .gch-mobile-accordion .accordion-body {
            background: #000 !important;
            color: #fff !important;
            border-color: rgba(255,255,255,.16) !important;
        }

        .gch-mobile-accordion .accordion-button {
            padding: 18px 22px;
            color: #fff !important;
            font-size: 17px;
            font-weight: 500;
            box-shadow: none !important;
        }

        .gch-mobile-accordion .accordion-button:not(.collapsed) {
            background: #111 !important;
        }

        .gch-mobile-accordion .accordion-button::after {
            filter: invert(1);
        }

        .gch-mobile-link {
            display: block;
            padding: 12px 35px;
            color: #ddd !important;
            text-decoration: none;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .gch-mobile-link:hover {
            background: #151515;
            color: #fff !important;
        }

        .gch-mobile-sale {
            color: #ff3030 !important;
        }

        /* Never crop clothing/catalogue images inside product cards or galleries. */
        .card-image img,
        .main-image img,
        .thumb img {
            object-fit: contain !important;
            background: #fff;
        }

        .card-image {
            background: #fff !important;
        }

        .main-image {
            background: #fff !important;
        }

        @media (max-width: 991.98px) {
            .gch-logo img {
                width: 185px;
            }

            .gch-header-row {
                min-height: 54px;
            }

            .gch-logo {
                top: 27px;
            }

            .gch-contact,
            .gch-header-main .d-flex:last-child {
                min-width: auto;
            }

            .gch-header-main .d-flex:last-child {
                gap: 8px !important;
            }

            .gch-nav {
                min-height: 48px;
            }

            .gch-nav .container-fluid {
                min-height: 48px;
            }
        }

        @media (max-width: 575.98px) {
            .gch-header-row {
                min-height: 52px;
            }

            .gch-logo img {
                width: 155px;
                max-height: 32px;
            }

            .gch-logo {
                top: 26px;
            }

            .gch-header-icon,
            .gch-whatsapp {
                width: 26px;
                height: 26px;
                font-size: 20px;
            }

            .gch-whatsapp {
                font-size: 23px;
            }

            .gch-toggler {
                width: 38px;
                height: 38px;
            }
        }
    </style>

    @if (isset($schema))
        <script type="application/ld+json">{!! $schema !!}</script>
    @endif
</head>
<body>
    <header class="site-header gch-bootstrap-header">
        <div class="gch-header-main">
            <div class="container-fluid px-3 px-lg-4">
                <div class="d-flex align-items-center justify-content-between gch-header-row">
                    <div class="gch-contact d-flex align-items-center gap-3">
                        @if (config('store.contact_phone'))
                            <a class="gch-header-icon" href="tel:{{ config('store.contact_phone') }}" aria-label="Call Golden Cloth House"><i class="bi bi-telephone"></i></a>
                        @endif
                        @if (config('store.whatsapp'))
                            <a class="gch-whatsapp" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('store.whatsapp')) }}" target="_blank" rel="noopener" aria-label="WhatsApp Golden Cloth House"><i class="bi bi-whatsapp"></i></a>
                        @endif
                    </div>

                    <a class="gch-logo" href="{{ route('home') }}" aria-label="{{ $siteName }} home">
                        <img src="{{ asset('images/golden-cloth-house-logo-header.svg') }}" alt="گولڈن کلاتھ ہاؤس">
                    </a>

                    <div class="d-flex align-items-center gap-3">
                        <form class="gch-search d-none d-md-flex" method="get" action="{{ route('shop') }}">
                            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search" aria-label="Search products">
                            <button type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
                        </form>
                        <a class="gch-header-icon d-none d-sm-inline-flex" href="{{ route('login') }}" aria-label="Account"><i class="bi bi-person"></i></a>
                        <a class="gch-header-icon gch-bag" href="{{ route('cart.index') }}" aria-label="Shopping bag">
                            <i class="bi bi-bag"></i>
                            @if ($cartCount > 0)<span class="cart-count">{{ $cartCount }}</span>@endif
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <nav class="navbar navbar-expand-lg navbar-dark gch-nav" aria-label="Primary navigation">
            <div class="container-fluid px-3 px-lg-4 position-relative">
                <button class="navbar-toggler border-0 shadow-none p-1 gch-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#gchMobileMenu" aria-controls="gchMobileMenu" aria-label="Open menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-center d-none d-lg-flex">
                    <ul class="navbar-nav align-items-center gap-lg-4">
                        <li class="nav-item"><a class="nav-link gch-nav-link" href="{{ route('shop', ['sort' => 'newest']) }}">New In</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link gch-nav-link dropdown-toggle" href="{{ route('shop', ['category' => 'women-unstitched']) }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Women</a>
                            <ul class="dropdown-menu gch-dropdown">
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
                            <a class="nav-link gch-nav-link dropdown-toggle" href="{{ route('shop', ['category' => 'men-unstitched']) }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Men</a>
                            <ul class="dropdown-menu gch-dropdown">
                                <li><a class="dropdown-item" href="{{ route('shop', ['category' => 'men-unstitched']) }}">All Men Unstitched</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Suit', 'category' => 'men-unstitched']) }}">Unstitched Suits</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Cotton', 'category' => 'men-unstitched']) }}">Cotton</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'men-unstitched']) }}">Khaddar</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link gch-nav-link gch-sale" href="{{ route('shop', ['sale' => 1]) }}">Sale</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="offcanvas offcanvas-start gch-offcanvas" tabindex="-1" id="gchMobileMenu" aria-labelledby="gchMobileMenuLabel">
            <div class="offcanvas-header border-bottom">
                <a class="gch-mobile-logo" href="{{ route('home') }}" aria-label="{{ $siteName }} home"><img src="{{ asset('images/golden-cloth-house-logo-header.svg') }}" alt="گولڈن کلاتھ ہاؤس"></a>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                <div class="accordion accordion-flush gch-mobile-accordion" id="gchMobileAccordion">
                    <div class="accordion-item"><a class="accordion-button collapsed text-decoration-none" href="{{ route('shop', ['sort' => 'newest']) }}">New In</a></div>
                    <div class="accordion-item">
                        <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#gchWomen">Women</button></h2>
                        <div id="gchWomen" class="accordion-collapse collapse" data-bs-parent="#gchMobileAccordion">
                            <div class="accordion-body p-0">
                                <a class="gch-mobile-link fw-semibold" href="{{ route('shop', ['category' => 'women-unstitched']) }}">All Women Unstitched</a>
                                <a class="gch-mobile-link" href="{{ route('shop', ['q' => '2 Piece', 'category' => 'women-unstitched']) }}">2 Piece Suits</a>
                                <a class="gch-mobile-link" href="{{ route('shop', ['q' => '3 Piece', 'category' => 'women-unstitched']) }}">3 Piece Suits</a>
                                <a class="gch-mobile-link" href="{{ route('shop', ['q' => 'Embroidered', 'category' => 'women-unstitched']) }}">Embroidered</a>
                                <a class="gch-mobile-link" href="{{ route('shop', ['q' => 'Lawn', 'category' => 'women-unstitched']) }}">Lawn</a>
                                <a class="gch-mobile-link" href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'women-unstitched']) }}">Khaddar</a>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#gchMen">Men</button></h2>
                        <div id="gchMen" class="accordion-collapse collapse" data-bs-parent="#gchMobileAccordion">
                            <div class="accordion-body p-0">
                                <a class="gch-mobile-link fw-semibold" href="{{ route('shop', ['category' => 'men-unstitched']) }}">All Men Unstitched</a>
                                <a class="gch-mobile-link" href="{{ route('shop', ['q' => 'Suit', 'category' => 'men-unstitched']) }}">Unstitched Suits</a>
                                <a class="gch-mobile-link" href="{{ route('shop', ['q' => 'Cotton', 'category' => 'men-unstitched']) }}">Cotton</a>
                                <a class="gch-mobile-link" href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'men-unstitched']) }}">Khaddar</a>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item"><a class="accordion-button text-decoration-none gch-mobile-sale" href="{{ route('shop', ['sale' => 1]) }}">Sale</a></div>
                </div>
            </div>
        </div>
    </header>

    <main>
        @if ($errors->any())
            <div class="container"><div class="error-box">{{ $errors->first() }}</div></div>
        @endif
        @if (session('success'))
            <div class="container"><div class="success-box">{{ session('success') }}</div></div>
        @endif
        @yield('content')
    </main>

    <section class="newsletter">
        <div class="container">
            <span class="section-kicker">Stay in the loop</span>
            <h2>New drops. Seasonal fabrics. Special offers.</h2>
            <p>Subscribe for new unstitched collections and exclusive updates from {{ $siteName }}.</p>
            <form class="newsletter-form" action="#" method="post" onsubmit="return false;">
                <input class="input" type="email" placeholder="Enter your email address" aria-label="Email address">
                <button class="btn" type="submit">Subscribe</button>
            </form>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand">گولڈن کلاتھ ہاؤس</div>
                    <p>خواتین اور مردوں کے معیاری ان سٹچڈ ملبوسات، پاکستان بھر میں Cash on Delivery کے ساتھ۔</p>
                    @if (config('store.contact_phone'))<p>{{ config('store.contact_phone') }}</p>@endif
                    @if (config('store.contact_email'))<p>{{ config('store.contact_email') }}</p>@endif
                </div>
                <div><div class="footer-title">Shop</div><div class="footer-links">
                    <a href="{{ route('shop', ['sort' => 'newest']) }}">New Arrivals</a>
                    <a href="{{ route('shop', ['category' => 'women-unstitched']) }}">Women Unstitched</a>
                    <a href="{{ route('shop', ['category' => 'men-unstitched']) }}">Men Unstitched</a>
                    <a href="{{ route('shop', ['sale' => 1]) }}">Sale</a>
                </div></div>
                <div><div class="footer-title">Customer Care</div><div class="footer-links">
                    <a href="{{ route('order.track') }}">Track Order</a>
                    <a href="{{ route('faq') }}">FAQs</a>
                    <a href="{{ route('contact') }}">Contact Us</a>
                    <a href="{{ route('privacy') }}">Privacy Policy</a>
                    <a href="{{ route('terms') }}">Terms & Conditions</a>
                </div></div>
                <div><div class="footer-title">About</div><div class="footer-links">
                    <a href="{{ route('about') }}">About {{ $siteName }}</a>
                    <a href="{{ route('shop') }}">All Products</a>
                    @if (config('store.instagram'))<a href="{{ config('store.instagram') }}" target="_blank" rel="noopener">Instagram</a>@endif
                    @if (config('store.facebook'))<a href="{{ config('store.facebook') }}" target="_blank" rel="noopener">Facebook</a>@endif
                    @if (config('store.tiktok'))<a href="{{ config('store.tiktok') }}" target="_blank" rel="noopener">TikTok</a>@endif
                </div></div>
            </div>
            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</span>
                <span>Cash on Delivery across Pakistan</span>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
