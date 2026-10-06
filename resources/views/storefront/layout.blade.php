<!doctype html>
<html lang="en">
<head>
    @php
        $siteName = config('store.name');
        $pageTitle = $title ?? $siteName;
        $pageDescription = $description ?? 'Golden Cloth House — women and men unstitched wear with Cash on Delivery across Pakistan.';
        $canonical = $canonical ?? url()->current();
        $shareImage = $shareImage ?? null;
        $cartCount = collect(session('cart', []))->sum('quantity');
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="{{ $canonical }}">
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

    @if (isset($schema))
        <script type="application/ld+json">{!! $schema !!}</script>
    @endif
</head>
<body>
    <div class="announcement">
        Free delivery on orders above {{ number_format(config('store.free_shipping_threshold')) }} {{ config('store.currency') }} · Cash on Delivery available across Pakistan
    </div>

    <div class="utility-bar">
        <div class="container utility-inner">
            <div class="utility-links">
                <a href="{{ route('about') }}">About Us</a>
                <a href="{{ route('faq') }}">FAQs</a>
                <a href="{{ route('contact') }}">Contact Us</a>
            </div>
            <div class="utility-links">
                <a href="{{ route('order.track') }}">Track Order</a>
                <a href="{{ route('shop', ['sort' => 'newest']) }}">New Arrivals</a>
            </div>
        </div>
    </div>

    <header class="site-header">
        <div class="container header-main">
            <button class="mobile-menu" type="button" aria-label="Open menu" data-mobile-menu>☰</button>

            <form class="header-search" method="get" action="{{ route('shop') }}">
                <input type="search" name="q" placeholder="Search unstitched wear..." aria-label="Search products">
                <button type="submit" aria-label="Search">⌕</button>
            </form>

            <a class="brand" href="{{ route('home') }}" aria-label="{{ $siteName }} home">
                <img src="{{ asset('images/golden-cloth-house-logo.svg') }}" alt="{{ $siteName }}" style="width:190px;height:auto;display:block;">
            </a>

            <div class="header-actions">
                <a class="icon-link" href="{{ route('order.track') }}"><span>⌖</span><span class="label">Track</span></a>
                <a class="icon-link" href="{{ route('cart.index') }}"><span>Bag</span><span class="cart-count">{{ $cartCount }}</span></a>
            </div>
        </div>

        <nav class="main-nav" aria-label="Primary navigation">
            <div class="container nav-inner">
                <a class="nav-link" href="{{ route('home') }}">Home</a>
                <div class="nav-item">
                    <a class="nav-link" href="{{ route('shop', ['category' => 'women-unstitched']) }}">Women</a>
                    <div class="mega-panel">
                        <div class="container mega-inner">
                            <div><div class="mega-title">Women Unstitched</div><div class="mega-links">
                                <a href="{{ route('shop', ['q' => '2 Piece', 'category' => 'women-unstitched']) }}">2 Piece Suits</a>
                                <a href="{{ route('shop', ['q' => '3 Piece', 'category' => 'women-unstitched']) }}">3 Piece Suits</a>
                                <a href="{{ route('shop', ['q' => 'Shirt', 'category' => 'women-unstitched']) }}">Unstitched Shirts</a>
                                <a href="{{ route('shop', ['q' => 'Dupatta', 'category' => 'women-unstitched']) }}">Dupattas</a>
                            </div></div>
                            <div><div class="mega-title">By Design</div><div class="mega-links">
                                <a href="{{ route('shop', ['q' => 'Printed', 'category' => 'women-unstitched']) }}">Printed</a>
                                <a href="{{ route('shop', ['q' => 'Embroidered', 'category' => 'women-unstitched']) }}">Embroidered</a>
                                <a href="{{ route('shop', ['q' => 'Lawn', 'category' => 'women-unstitched']) }}">Lawn</a>
                                <a href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'women-unstitched']) }}">Khaddar</a>
                            </div></div>
                            <div><div class="mega-title">Shop Women</div><div class="mega-links">
                                <a href="{{ route('shop', ['category' => 'women-unstitched']) }}">All Women Unstitched</a>
                                <a href="{{ route('shop', ['sort' => 'newest']) }}">New Arrivals</a>
                                <a href="{{ route('shop', ['sort' => 'price_low']) }}">Budget Picks</a>
                            </div></div>
                            <div><div class="mega-title">Customer Care</div><div class="mega-links">
                                <a href="{{ route('order.track') }}">Track Your Order</a>
                                <a href="{{ route('faq') }}">FAQs</a>
                                <a href="{{ route('contact') }}">Contact Us</a>
                            </div></div>
                        </div>
                    </div>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="{{ route('shop', ['category' => 'men-unstitched']) }}">Men</a>
                    <div class="mega-panel">
                        <div class="container mega-inner">
                            <div><div class="mega-title">Men Unstitched</div><div class="mega-links">
                                <a href="{{ route('shop', ['category' => 'men-unstitched']) }}">All Men Unstitched</a>
                                <a href="{{ route('shop', ['q' => 'Suit', 'category' => 'men-unstitched']) }}">Unstitched Suits</a>
                                <a href="{{ route('shop', ['q' => 'Shalwar', 'category' => 'men-unstitched']) }}">Shalwar / Trouser</a>
                                <a href="{{ route('shop', ['q' => 'Wash', 'category' => 'men-unstitched']) }}">Wash & Wear</a>
                            </div></div>
                            <div><div class="mega-title">By Fabric</div><div class="mega-links">
                                <a href="{{ route('shop', ['q' => 'Cotton', 'category' => 'men-unstitched']) }}">Cotton</a>
                                <a href="{{ route('shop', ['q' => 'Linen', 'category' => 'men-unstitched']) }}">Linen</a>
                                <a href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'men-unstitched']) }}">Khaddar</a>
                            </div></div>
                            <div><div class="mega-title">Shop Men</div><div class="mega-links">
                                <a href="{{ route('shop', ['category' => 'men-unstitched']) }}">Men's Collection</a>
                                <a href="{{ route('shop', ['sort' => 'newest']) }}">New Arrivals</a>
                            </div></div>
                            <div><div class="mega-title">Customer Care</div><div class="mega-links">
                                <a href="{{ route('order.track') }}">Track Your Order</a>
                                <a href="{{ route('faq') }}">FAQs</a>
                                <a href="{{ route('contact') }}">Contact Us</a>
                            </div></div>
                        </div>
                    </div>
                </div>
                <a class="nav-link" href="{{ route('shop', ['sort' => 'newest']) }}">New In</a>
                <a class="nav-link sale-link" href="{{ route('shop', ['q' => 'Sale']) }}">Sale</a>
                <a class="nav-link" href="{{ route('order.track') }}">Track Order</a>
            </div>
        </nav>
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
                    <div class="footer-brand">{{ $siteName }}</div>
                    <p>Modern women and men unstitched wear, delivered across Pakistan with Cash on Delivery.</p>
                    @if (config('store.contact_phone'))<p>{{ config('store.contact_phone') }}</p>@endif
                    @if (config('store.contact_email'))<p>{{ config('store.contact_email') }}</p>@endif
                </div>
                <div><div class="footer-title">Shop</div><div class="footer-links">
                    <a href="{{ route('shop', ['sort' => 'newest']) }}">New Arrivals</a>
                    <a href="{{ route('shop', ['category' => 'women-unstitched']) }}">Women Unstitched</a>
                    <a href="{{ route('shop', ['category' => 'men-unstitched']) }}">Men Unstitched</a>
                    <a href="{{ route('shop', ['q' => 'Sale']) }}">Sale</a>
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
                <span>© {{ now()->year }} {{ $siteName }}. All rights reserved.</span>
                <span>Secure checkout · Cash on Delivery · Pakistan</span>
            </div>
        </div>
    </footer>

    @if (config('store.whatsapp'))
        <a class="floating-whatsapp" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('store.whatsapp')) }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">◉</a>
    @endif

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuButton = document.querySelector('[data-mobile-menu]');
            if (!menuButton) return;
            menuButton.addEventListener('click', () => {
                const nav = document.querySelector('.main-nav');
                nav.style.display = nav.style.display === 'block' ? '' : 'block';
            });
        });
    </script>
</body>
</html>
