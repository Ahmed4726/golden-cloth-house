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
        .quick-view-button { width: 2.75rem; height: 2.75rem; padding: 0; }

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
                        @if (config('store.whatsapp'))
                            <a class="text-success fs-4 d-none d-lg-inline-block" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('store.whatsapp')) }}" target="_blank" rel="noopener" aria-label="WhatsApp Golden Cloth House">
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
                            <ul class="dropdown-menu shadow border-0 rounded-0">
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
                            <ul class="dropdown-menu shadow border-0 rounded-0">
                                <li><a class="dropdown-item" href="{{ route('shop', ['category' => 'men-unstitched']) }}">All Men Unstitched</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Suit', 'category' => 'men-unstitched']) }}">Unstitched Suits</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Cotton', 'category' => 'men-unstitched']) }}">Cotton</a></li>
                                <li><a class="dropdown-item" href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'men-unstitched']) }}">Khaddar</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase fw-medium text-danger" href="{{ route('shop', ['sort' => 'price_asc']) }}">Sale</a>
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
                <div class="accordion accordion-flush" id="gchMobileAccordion">
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
                                <a class="d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['category' => 'women-unstitched']) }}">All Women Unstitched</a>
                                <a class="d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => '2 Piece', 'category' => 'women-unstitched']) }}">2 Piece Suits</a>
                                <a class="d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => '3 Piece', 'category' => 'women-unstitched']) }}">3 Piece Suits</a>
                                <a class="d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Embroidered', 'category' => 'women-unstitched']) }}">Embroidered</a>
                                <a class="d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Lawn', 'category' => 'women-unstitched']) }}">Lawn</a>
                                <a class="d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'women-unstitched']) }}">Khaddar</a>
                                <a class="d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Slub', 'category' => 'women-unstitched']) }}">Slub</a>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-dark text-white">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-dark text-white" type="button" data-bs-toggle="collapse" data-bs-target="#menMenu">Men</button>
                        </h2>
                        <div id="menMenu" class="accordion-collapse collapse" data-bs-parent="#gchMobileAccordion">
                            <div class="accordion-body p-0">
                                <a class="d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['category' => 'men-unstitched']) }}">All Men Unstitched</a>
                                <a class="d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Suit', 'category' => 'men-unstitched']) }}">Unstitched Suits</a>
                                <a class="d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Cotton', 'category' => 'men-unstitched']) }}">Cotton</a>
                                <a class="d-block px-4 py-3 text-white text-decoration-none border-top border-secondary" href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'men-unstitched']) }}">Khaddar</a>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-dark text-white">
                        <h2 class="accordion-header">
                            <a class="accordion-button bg-dark text-danger text-decoration-none" href="{{ route('shop', ['sort' => 'price_asc']) }}">Sale</a>
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
                    @if (config('store.whatsapp'))
                        <a class="link-success text-decoration-none" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('store.whatsapp')) }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp me-2"></i>WhatsApp</a>
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

    @if (config('store.whatsapp'))
        <a class="btn btn-success rounded-circle position-fixed bottom-0 end-0 m-3 shadow" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('store.whatsapp')) }}" target="_blank" rel="noopener" aria-label="WhatsApp Golden Cloth House">
            <i class="fa-brands fa-whatsapp fs-5"></i>
        </a>
    @endif

    <div class="modal fade" id="productQuickView" tabindex="-1" aria-labelledby="productQuickViewTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-0 border-0">
                <div class="modal-header border-0">
                    <h2 class="modal-title h5" id="productQuickViewTitle">Product details</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6">
                            <div class="ratio ratio-3x4 bg-light">
                                <img id="quickViewImage" class="w-100 h-100 object-fit-contain" src="{{ asset('images/product-placeholder.svg') }}" alt="" loading="lazy">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="quickViewCategory" class="small text-uppercase text-secondary mb-2"></div>
                            <div id="quickViewPrice" class="h5 fw-bold mb-1"></div>
                            <div id="quickViewComparePrice" class="small text-secondary text-decoration-line-through mb-3 d-none"></div>
                            <p id="quickViewDescription" class="text-secondary"></p>
                            <div id="quickViewStock" class="small mb-4"></div>
                            <div class="d-flex flex-wrap gap-2">
                                <a id="quickViewLink" class="btn btn-outline-dark" href="#">View product</a>
                                <form id="quickViewCartForm" method="POST" action="#">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button id="quickViewAddButton" class="btn btn-dark" type="submit"><i class="fa-solid fa-cart-shopping me-2" aria-hidden="true"></i>Add to cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('productQuickView')?.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            const modal = event.currentTarget;
            const inStock = trigger.dataset.productInStock === 'true';
            const description = trigger.dataset.productDescription;
            const comparePrice = trigger.dataset.productComparePrice;

            modal.querySelector('#productQuickViewTitle').textContent = trigger.dataset.productName;
            modal.querySelector('#quickViewImage').src = trigger.dataset.productImage;
            modal.querySelector('#quickViewImage').alt = trigger.dataset.productName;
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
        });
    </script>
    @stack('scripts')
</body>
</html>
