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
    <header class="site-header">
        <div class="header-top">
            <div class="container header-top-inner">
                <div class="header-contact">
                    @if (config('store.contact_phone'))
                        <a class="header-contact-link" href="tel:{{ config('store.contact_phone') }}" aria-label="Call Golden Cloth House">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 2.8 9.1 2a1.4 1.4 0 0 1 1.7.8l1.4 3.4a1.4 1.4 0 0 1-.4 1.6L10 9.5a13.5 13.5 0 0 0 4.5 4.5l1.7-1.8a1.4 1.4 0 0 1 1.6-.3l3.4 1.4a1.4 1.4 0 0 1 .8 1.7l-.8 2.5a2.8 2.8 0 0 1-3 1.9C9.8 18.2 5.8 14.2 4.6 5.8a2.8 2.8 0 0 1 2-3Z"/></svg>
                        </a>
                    @endif
                    @if (config('store.whatsapp'))
                        <a class="header-whatsapp" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('store.whatsapp')) }}" target="_blank" rel="noopener" aria-label="WhatsApp Golden Cloth House">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 3.5A10.4 10.4 0 0 0 4.1 16.1L3 20.9l4.9-1.1a10.4 10.4 0 0 0 12.6-16.3ZM12 19.1c-1.6 0-3.1-.4-4.4-1.2l-.3-.2-2.9.7.7-2.8-.2-.3a8.5 8.5 0 1 1 7.1 3.8Zm4.7-6.4c-.3-.2-1.8-.9-2-.9-.3-.1-.5-.2-.7.2-.2.3-.8.9-.9 1.1-.2.2-.3.2-.6.1-1.6-.8-2.6-1.4-3.7-3.2-.3-.5.3-.5.8-1.5.1-.2.1-.4 0-.6 0-.2-.7-1.7-.9-2.3-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.2 1.1-1.2 2.7s1.2 3.1 1.4 3.3c.2.2 2.4 3.7 5.8 5.1 2.2.9 2.2.6 2.6.6.4 0 1.8-.7 2.1-1.3.3-.7.3-1.2.2-1.3-.1-.2-.3-.2-.6-.4Z"/></svg>
                        </a>
                    @endif
                </div>

                <a class="header-brand" href="{{ route('home') }}" aria-label="{{ $siteName }} home">
                    <img src="{{ asset('images/golden-cloth-house-logo-header.svg') }}" alt="گولڈن کلاتھ ہاؤس">
                </a>

                <div class="header-actions">
                    <form class="header-search" method="get" action="{{ route('shop') }}">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search" aria-label="Search products">
                        <button type="submit" aria-label="Search">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg>
                        </button>
                    </form>
                    <a class="header-icon" href="{{ route('login') }}" aria-label="Account">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7.5" r="3.7"/><path d="M4.2 21c.6-4 3.2-6.1 7.8-6.1s7.2 2.1 7.8 6.1"/></svg>
                    </a>
                    <a class="header-icon bag-icon" href="{{ route('cart.index') }}" aria-label="Shopping bag">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 8.5h14l-.7 12H5.7L5 8.5Z"/><path d="M8.5 8.5V6a3.5 3.5 0 0 1 7 0v2.5"/></svg>
                        @if ($cartCount > 0)<span class="cart-count">{{ $cartCount }}</span>@endif
                    </a>
                </div>
            </div>
        </div>

        <nav class="main-nav" aria-label="Primary navigation">
            <div class="container nav-inner">
                <button class="mobile-menu" type="button" aria-label="Open menu" aria-expanded="false" data-mobile-menu>
                    <span></span><span></span><span></span>
                </button>

                <div class="nav-links">
                    <a class="nav-link" href="{{ route('shop', ['sort' => 'newest']) }}">New In</a>
                    <div class="nav-item">
                        <a class="nav-link" href="{{ route('shop', ['category' => 'women-unstitched']) }}">Women</a>
                        <div class="mega-panel">
                            <div class="container mega-inner">
                                <div><div class="mega-title">Women Unstitched</div><div class="mega-links">
                                    <a href="{{ route('shop', ['category' => 'women-unstitched']) }}">All Women Unstitched</a>
                                    <a href="{{ route('shop', ['q' => '2 Piece', 'category' => 'women-unstitched']) }}">2 Piece Suits</a>
                                    <a href="{{ route('shop', ['q' => '3 Piece', 'category' => 'women-unstitched']) }}">3 Piece Suits</a>
                                    <a href="{{ route('shop', ['q' => 'Embroidered', 'category' => 'women-unstitched']) }}">Embroidered</a>
                                </div></div>
                                <div><div class="mega-title">By Fabric</div><div class="mega-links">
                                    <a href="{{ route('shop', ['q' => 'Lawn', 'category' => 'women-unstitched']) }}">Lawn</a>
                                    <a href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'women-unstitched']) }}">Khaddar</a>
                                    <a href="{{ route('shop', ['q' => 'Slub', 'category' => 'women-unstitched']) }}">Slub</a>
                                </div></div>
                                <div><div class="mega-title">Shop</div><div class="mega-links">
                                    <a href="{{ route('shop', ['sort' => 'newest']) }}">New Arrivals</a>
                                    <a href="{{ route('shop', ['sort' => 'price_low']) }}">Budget Picks</a>
                                    <a href="{{ route('shop', ['sale' => 1]) }}">Sale</a>
                                </div></div>
                                <div><div class="mega-title">Customer Care</div><div class="mega-links">
                                    <a href="{{ route('order.track') }}">Track Order</a>
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
                                    <a href="{{ route('shop', ['q' => 'Cotton', 'category' => 'men-unstitched']) }}">Cotton</a>
                                    <a href="{{ route('shop', ['q' => 'Khaddar', 'category' => 'men-unstitched']) }}">Khaddar</a>
                                </div></div>
                                <div><div class="mega-title">Shop</div><div class="mega-links">
                                    <a href="{{ route('shop', ['sort' => 'newest']) }}">New Arrivals</a>
                                    <a href="{{ route('shop', ['sale' => 1]) }}">Sale</a>
                                </div></div>
                                <div><div class="mega-title">Customer Care</div><div class="mega-links">
                                    <a href="{{ route('order.track') }}">Track Order</a>
                                    <a href="{{ route('faq') }}">FAQs</a>
                                    <a href="{{ route('contact') }}">Contact Us</a>
                                </div></div>
                            </div>
                        </div>
                    </div>
                    <a class="nav-link sale-link" href="{{ route('shop', ['sale' => 1]) }}">Sale</a>
                </div>
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
            const navLinks = document.querySelector('.nav-links');

            if (menuButton && navLinks) {
                menuButton.addEventListener('click', () => {
                    const open = navLinks.classList.toggle('is-open');
                    menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
            }
        });
    </script>
</body>
</html>
