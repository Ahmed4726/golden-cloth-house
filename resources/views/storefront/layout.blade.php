<!doctype html>
<html lang="en-PK">
<head>
    <script async src="https://www.googletagmanager.com/gtm.js?id=GTM-P6ZTCG4X"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    </script>
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
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .product-image-zoom {
            transition: transform 350ms ease;
        }

        .product-image-viewer-trigger {
            cursor: zoom-in;
        }

        .product-image-viewer-stage {
            position: relative;
            height: min(70vh, 50rem);
            overflow: auto;
        }

        .product-image-viewer-image {
            display: block;
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: calc(100dvh - 8rem);
            object-fit: contain;
            cursor: zoom-in;
        }

        .product-image-viewer-stage.is-zoomed {
            display: block;
        }

        .product-image-viewer-image.is-zoomed {
            width: 175%;
            max-width: none;
            max-height: none;
            cursor: zoom-out;
        }

        .product-image-viewer-control {
            position: absolute;
            z-index: 2;
            top: 50%;
            transform: translateY(-50%);
        }

        .product-image-viewer-control-previous {
            left: 0.75rem;
        }

        .product-image-viewer-control-next {
            right: 0.75rem;
        }

        .product-detail-gallery .carousel-control-prev,
        .product-detail-gallery .carousel-control-next {
            top: 50%;
            bottom: auto;
            width: 2.75rem;
            height: 2.75rem;
            transform: translateY(-50%);
            border-radius: 50%;
            background-color: rgb(33 37 41 / 75%);
            opacity: 1;
            z-index: 2;
        }

        .product-detail-gallery .carousel-control-prev {
            left: 0.75rem;
        }

        .product-detail-gallery .carousel-control-next {
            right: 0.75rem;
        }

        @media (max-width: 767.98px) {
            .product-image-viewer-stage {
                height: calc(100dvh - 5rem);
            }

            .product-image-viewer-control-previous {
                left: 0.5rem;
            }

            .product-image-viewer-control-next {
                right: 0.5rem;
            }
        }

        @media (hover: hover) {
            .product-image-zoom:hover {
                transform: scale(1.12);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .product-image-zoom {
                transition: none;
            }
        }
    </style>

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
    @stack('head')
</head>
<body class="bg-white text-dark">
    <a class="visually-hidden-focusable position-absolute top-0 start-0 p-2 bg-white text-dark z-3" href="#main-content">Skip to content</a>
    <header class="sticky-top shadow-sm">
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

        <nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-secondary" aria-label="Primary navigation">
            <div class="container-fluid px-3 px-lg-4">
                <div class="collapse navbar-collapse justify-content-center d-none d-lg-flex">
                    <ul class="navbar-nav align-items-center gap-lg-4">
                        <li class="nav-item">
                            <a class="nav-link text-uppercase fw-medium" href="{{ route('shop', ['sort' => 'newest']) }}">New In</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link text-uppercase fw-medium dropdown-toggle" href="{{ route('shop', ['category' => 'women-unstitched']) }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Women</a>
                            <ul class="dropdown-menu">
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
                            <ul class="dropdown-menu">
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
                <div class="accordion accordion-flush" id="gchMobileAccordion" data-bs-theme="dark">
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

    @if (request()->routeIs('shop.product'))
        <div class="modal fade" id="productImageViewer" tabindex="-1" aria-labelledby="productImageViewerTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-md-down">
                <div class="modal-content bg-dark text-white border-0">
                    <div class="modal-header border-0">
                        <div>
                            <h2 class="modal-title h6 mb-1" id="productImageViewerTitle">Product images</h2>
                            <span class="small text-white-50" id="productImageViewerCounter"></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-outline-light btn-sm" id="productImageViewerZoom" type="button" aria-label="Zoom image">
                                <i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close image viewer"></button>
                        </div>
                    </div>
                    <div class="modal-body product-image-viewer-stage d-flex align-items-center justify-content-center p-0">
                        <button class="btn btn-dark product-image-viewer-control product-image-viewer-control-previous" id="productImageViewerPrevious" type="button" aria-label="Previous image">
                            <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                        </button>
                        <img class="product-image-viewer-image" id="productImageViewerImage" src="" alt="">
                        <button class="btn btn-dark product-image-viewer-control product-image-viewer-control-next" id="productImageViewerNext" type="button" aria-label="Next image">
                            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="productQuickView" data-whatsapp-number="{{ $whatsappNumber ? preg_replace('/[^0-9]/', '', $whatsappNumber) : '' }}" data-shipping-fee="{{ config('store.shipping_fee') }}" data-free-shipping-threshold="{{ config('store.free_shipping_threshold') }}" tabindex="-1" aria-labelledby="productQuickViewTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-3 shadow-lg">
                <div class="modal-header border-0 px-4 pt-4">
                    <h2 class="modal-title h5" id="productQuickViewTitle">Product details</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6">
                            <div>
                                <div
                                    id="quickViewGallery"
                                    class="carousel slide quick-view-gallery ratio ratio-4x3 bg-light rounded overflow-hidden"
                                    data-bs-interval="false"
                                    data-bs-touch="true"
                                    aria-label="Product images"
                                >
                                    <div id="quickViewGalleryItems" class="carousel-inner h-100">
                                        <div class="carousel-item h-100 active">
                                            <img id="quickViewImage" class="d-block w-100 h-100 object-fit-contain product-image-zoom" src="{{ asset('images/product-placeholder.svg') }}" alt="" loading="lazy">
                                        </div>
                                    </div>
                                    <button class="carousel-control-prev d-none" type="button" data-bs-target="#quickViewGallery" data-bs-slide="prev" aria-label="Previous product image">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    </button>
                                    <button class="carousel-control-next d-none" type="button" data-bs-target="#quickViewGallery" data-bs-slide="next" aria-label="Next product image">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    </button>
                                </div>
                                <div id="quickViewGalleryIndicators" class="carousel-indicators position-static justify-content-center gap-0 w-100 m-0 mt-2 d-none"></div>
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
                                <form id="quickViewCartForm" class="row g-2 align-items-end" method="POST" action="#">
                                    @csrf
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold mb-1" for="quickViewQuantity">Quantity</label>
                                        <input id="quickViewQuantity" class="form-control" type="number" name="quantity" value="1" min="1" max="20" required>
                                    </div>
                                    <div class="col">
                                        <button id="quickViewAddButton" class="btn btn-dark w-100 fw-semibold" type="submit"><i class="fa-solid fa-cart-shopping me-2" aria-hidden="true"></i>Add to cart</button>
                                    </div>
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
        const productImageViewer = document.getElementById('productImageViewer');
        if (productImageViewer) {
            const productGallery = document.querySelector('[data-image-viewer-gallery]');
            const productImages = JSON.parse(productGallery?.dataset.imageViewerGallery || '[]');
            const productName = productGallery?.dataset.imageViewerTitle || 'Product';
            const viewerImage = productImageViewer.querySelector('#productImageViewerImage');
            const viewerCounter = productImageViewer.querySelector('#productImageViewerCounter');
            const viewerStage = productImageViewer.querySelector('.product-image-viewer-stage');
            const zoomButton = productImageViewer.querySelector('#productImageViewerZoom');
            const zoomIcon = zoomButton.querySelector('i');
            const previousButton = productImageViewer.querySelector('#productImageViewerPrevious');
            const nextButton = productImageViewer.querySelector('#productImageViewerNext');
            let currentImageIndex = 0;

            const displayImage = (index) => {
                if (productImages.length === 0) {
                    return;
                }

                currentImageIndex = (index + productImages.length) % productImages.length;
                viewerImage.src = productImages[currentImageIndex];
                viewerImage.alt = `${productName} image ${currentImageIndex + 1}`;
                viewerImage.classList.remove('is-zoomed');
                viewerStage.classList.remove('is-zoomed');
                viewerStage.classList.add('d-flex');
                viewerStage.scrollTo(0, 0);
                zoomButton.setAttribute('aria-label', 'Zoom image');
                zoomIcon.classList.replace('fa-magnifying-glass-minus', 'fa-magnifying-glass-plus');
                viewerCounter.textContent = `${currentImageIndex + 1} / ${productImages.length}`;
            };

            const toggleZoom = () => {
                const isZoomed = viewerImage.classList.toggle('is-zoomed');
                viewerStage.classList.toggle('is-zoomed', isZoomed);
                viewerStage.classList.toggle('d-flex', !isZoomed);
                zoomButton.setAttribute('aria-label', isZoomed ? 'Zoom out' : 'Zoom image');
                zoomIcon.classList.toggle('fa-magnifying-glass-plus', !isZoomed);
                zoomIcon.classList.toggle('fa-magnifying-glass-minus', isZoomed);
            };

            previousButton.classList.toggle('d-none', productImages.length < 2);
            nextButton.classList.toggle('d-none', productImages.length < 2);
            viewerCounter.classList.toggle('d-none', productImages.length < 2);

            productGallery?.addEventListener('click', (event) => {
                const trigger = event.target.closest('.product-image-viewer-trigger');

                if (!trigger) {
                    return;
                }

                displayImage(Number(trigger.dataset.imageViewerIndex));
                bootstrap.Modal.getOrCreateInstance(productImageViewer).show();
            });

            previousButton.addEventListener('click', () => displayImage(currentImageIndex - 1));
            nextButton.addEventListener('click', () => displayImage(currentImageIndex + 1));
            zoomButton.addEventListener('click', toggleZoom);
            viewerImage.addEventListener('click', toggleZoom);

            let touchStartX = null;
            viewerStage.addEventListener('pointerdown', (event) => {
                touchStartX = event.pointerType === 'touch' && !viewerImage.classList.contains('is-zoomed')
                    ? event.clientX
                    : null;
            });
            viewerStage.addEventListener('pointerup', (event) => {
                if (touchStartX === null) {
                    return;
                }

                const swipeDistance = event.clientX - touchStartX;
                touchStartX = null;

                if (Math.abs(swipeDistance) >= 50) {
                    displayImage(currentImageIndex + (swipeDistance < 0 ? 1 : -1));
                }
            });
            viewerStage.addEventListener('pointercancel', () => {
                touchStartX = null;
            });
        }

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
                imageElement.className = 'd-block w-100 h-100 object-fit-contain product-image-zoom';
                imageElement.src = image;
                imageElement.alt = `${trigger.dataset.productName} image ${index + 1}`;
                imageElement.loading = index === 0 ? 'eager' : 'lazy';
                item.appendChild(imageElement);
                galleryItems.appendChild(item);

                if (galleryImages.length > 1) {
                    const indicator = document.createElement('button');
                    indicator.type = 'button';
                    indicator.className = 'bg-dark rounded-pill';
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
            modal.querySelector('#quickViewStock').textContent = inStock ? 'In stock' : 'Sold out';
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
                const unitPrice = Number(trigger.dataset.productAmount);
                const currency = trigger.dataset.productCurrency;
                const subtotal = unitPrice * quantity;
                const shippingFee = Number(modal.dataset.shippingFee);
                const shippingThreshold = Number(modal.dataset.freeShippingThreshold);
                const shipping = subtotal > 0 && subtotal < shippingThreshold ? shippingFee : 0;
                const formatAmount = (amount) => Math.round(amount).toLocaleString('en-US');
                const message = [
                    'Hello, I would like to order this product:',
                    trigger.dataset.productName,
                    `Quantity: ${quantity}`,
                    `Unit price: ${trigger.dataset.productPrice}`,
                    `Subtotal: ${formatAmount(subtotal)} ${currency}`,
                    `Delivery: ${shipping > 0 ? `${formatAmount(shipping)} ${currency}` : 'Free'}`,
                    `Total: ${formatAmount(subtotal + shipping)} ${currency}`,
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
