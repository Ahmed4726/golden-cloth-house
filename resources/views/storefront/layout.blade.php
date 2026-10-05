<!doctype html>
<html lang="en">
<head>
    @php
        $siteName = config('store.name');
        $pageTitle = $title ?? $siteName;
        $pageDescription = $description ?? 'Shop the latest clothing collection from Golden Cloth House. Cash on Delivery available across Pakistan.';
        $canonical = $canonical ?? url()->current();
        $shareImage = $shareImage ?? null;
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

    @if (isset($schema))
        <script type="application/ld+json">{!! $schema !!}</script>
    @endif

    <style>
        :root {
            --ink: #17130f;
            --muted: #756d66;
            --paper: #faf8f4;
            --card: #ffffff;
            --line: #e8e1d8;
            --accent: #8b6a43;
            --accent-dark: #6d4f2f;
            --success: #166534;
            --danger: #b91c1c;
            --shadow: 0 16px 50px rgba(23, 19, 15, .08);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            background: var(--paper);
            color: var(--ink);
            font: 15px/1.6 Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        img { max-width: 100%; }
        button, input, select, textarea { font: inherit; }
        a { color: inherit; text-decoration: none; }
        .site { width: min(1240px, calc(100% - 32px)); margin-inline: auto; }

        .announcement {
            background: var(--ink);
            color: #fff;
            text-align: center;
            padding: 8px 16px;
            font-size: 13px;
        }

        .header {
            min-height: 82px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            border-bottom: 1px solid var(--line);
            background: rgba(250, 248, 244, .96);
            position: sticky;
            top: 0;
            z-index: 30;
            backdrop-filter: blur(12px);
        }

        .logo { font-weight: 900; letter-spacing: .1em; white-space: nowrap; }
        .nav { display: flex; align-items: center; gap: 22px; }
        .nav a { color: #4f4943; font-weight: 600; }
        .nav a:hover { color: var(--accent-dark); }
        .cart-link { display: inline-flex; align-items: center; gap: 8px; }
        .cart-count {
            min-width: 22px;
            height: 22px;
            padding: 0 6px;
            display: inline-grid;
            place-items: center;
            border-radius: 999px;
            background: var(--ink);
            color: #fff;
            font-size: 11px;
        }

        .hero {
            min-height: 520px;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            margin: 28px 0;
            border-radius: 20px;
            background: #eee7df;
        }
        .hero-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hero-overlay { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(20, 15, 10, .68), rgba(20, 15, 10, .08)); }
        .hero-content { position: relative; z-index: 2; color: #fff; max-width: 680px; padding: 70px 7%; }
        .hero-content p { color: rgba(255,255,255,.86); font-size: 18px; max-width: 600px; }
        .hero h1 { font-size: clamp(42px, 6vw, 76px); line-height: 1.02; margin: 14px 0 20px; }
        .eyebrow { font-size: 12px; font-weight: 800; letter-spacing: .18em; color: var(--accent); }
        .hero .eyebrow { color: #e7c998; }

        .section { padding: 48px 0; }
        .section-head { display: flex; justify-content: space-between; align-items: end; gap: 20px; margin-bottom: 24px; }
        h2 { font-size: 34px; line-height: 1.1; margin: 6px 0; }
        .product-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px; }
        .product-card { background: var(--card); border: 1px solid var(--line); border-radius: 14px; overflow: hidden; transition: transform .2s, box-shadow .2s; }
        .product-card:hover { transform: translateY(-3px); box-shadow: var(--shadow); }
        .card-image { aspect-ratio: 3 / 4; background: #eee7df; overflow: hidden; position: relative; }
        .card-image img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .35s; }
        .product-card:hover .card-image img { transform: scale(1.035); }
        .badge { position: absolute; top: 12px; left: 12px; background: var(--ink); color: #fff; padding: 5px 9px; border-radius: 999px; font-size: 11px; font-weight: 800; }
        .placeholder { height: 100%; display: grid; place-items: center; color: #9a8d80; }
        .card-body { padding: 15px; }
        .card-body h3 { margin: 5px 0 7px; font-size: 17px; }
        .muted, .sku { color: var(--muted); }
        .price { font-size: 18px; font-weight: 800; }
        .compare { color: var(--muted); text-decoration: line-through; margin-left: 7px; font-size: 14px; }
        .shop-btn, .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 16px;
            padding: 12px 20px;
            border: 1px solid var(--ink);
            border-radius: 8px;
            background: var(--ink);
            color: #fff;
            cursor: pointer;
            font-weight: 700;
        }
        .btn.secondary, .shop-btn.secondary { background: #fff; color: var(--ink); border-color: var(--line); }
        .btn.full { width: 100%; }
        .btn.danger { background: var(--danger); border-color: var(--danger); }
        .page-head { padding: 55px 0 25px; }
        .page-head p { font-size: 18px; color: var(--muted); max-width: 700px; }

        .filters { display: grid; grid-template-columns: 1fr 220px auto; gap: 12px; margin-bottom: 28px; }
        .input, .select, .textarea {
            width: 100%;
            border: 1px solid #d8d1c8;
            border-radius: 9px;
            background: #fff;
            padding: 12px 13px;
            outline: none;
        }
        .input:focus, .select:focus, .textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(139,106,67,.12); }
        .form-label { display: block; font-weight: 700; margin-bottom: 7px; }
        .field { margin-bottom: 18px; }
        .error-box, .success-box { padding: 13px 15px; border-radius: 9px; margin-bottom: 18px; }
        .error-box { background: #fef2f2; color: #991b1b; }
        .success-box { background: #ecfdf5; color: #166534; }

        .product-detail { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(360px, .95fr); gap: 60px; padding: 60px 0; }
        .gallery { min-width: 0; }
        .main-image { aspect-ratio: 1 / 1; background: #eee7df; overflow: hidden; border-radius: 14px; }
        .main-image img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .thumbs { display: grid; grid-template-columns: repeat(6, 1fr); gap: 9px; margin-top: 12px; }
        .thumb { aspect-ratio: 1; padding: 0; border: 2px solid transparent; border-radius: 8px; overflow: hidden; background: #eee7df; cursor: pointer; }
        .thumb.active { border-color: var(--ink); }
        .thumb img { width: 100%; height: 100%; object-fit: cover; }
        .details h1 { font-size: clamp(36px, 5vw, 58px); line-height: 1.05; margin: 10px 0 14px; }
        .details .price { font-size: 28px; margin: 18px 0; }
        .description { font-size: 17px; color: #554e48; margin: 25px 0; }
        .stock { color: var(--success); font-weight: 700; }
        .stock.out { color: var(--danger); }
        .quantity-row { display: flex; align-items: end; gap: 12px; margin-top: 20px; }
        .quantity { width: 110px; }

        .cart-layout, .checkout-layout { display: grid; grid-template-columns: minmax(0, 1fr) 360px; gap: 28px; align-items: start; }
        .cart-item, .summary, .checkout-card { background: #fff; border: 1px solid var(--line); border-radius: 14px; }
        .cart-item { display: grid; grid-template-columns: 92px 1fr auto auto; gap: 15px; align-items: center; padding: 14px; margin-bottom: 12px; }
        .cart-item img { width: 92px; height: 115px; object-fit: cover; border-radius: 8px; background: #eee7df; }
        .cart-qty { width: 76px; text-align: center; }
        .summary, .checkout-card { padding: 22px; }
        .summary { position: sticky; top: 105px; }
        .summary-row { display: flex; justify-content: space-between; gap: 16px; padding: 9px 0; }
        .summary-total { border-top: 1px solid var(--line); margin-top: 8px; padding-top: 15px; font-size: 20px; font-weight: 900; }
        .trust { margin-top: 16px; padding: 14px; background: #f7f2eb; border-radius: 10px; font-size: 13px; }
        .success { text-align: center; padding: 90px 0; }
        .success-icon { width: 70px; height: 70px; margin: 0 auto 20px; border-radius: 50%; display: grid; place-items: center; background: #dcfce7; color: #166534; font-size: 32px; }

        .footer { border-top: 1px solid var(--line); padding: 35px 0; color: var(--muted); margin-top: 60px; }
        .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 30px; }
        .footer-title { color: var(--ink); font-weight: 800; margin-bottom: 8px; }
        .footer-links { display: grid; gap: 7px; }
        .empty { padding: 50px; background: #fff; border: 1px dashed var(--line); border-radius: 12px; text-align: center; }
        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 30px; }
        .pagination a, .pagination span { padding: 8px 12px; background: #fff; border: 1px solid var(--line); border-radius: 7px; }

        @media (max-width: 950px) {
            .product-grid { grid-template-columns: repeat(3, 1fr); }
            .product-detail, .cart-layout, .checkout-layout { grid-template-columns: 1fr; }
            .summary { position: static; }
        }
        @media (max-width: 700px) {
            .header { flex-wrap: wrap; padding: 14px 0; }
            .nav { width: 100%; justify-content: space-between; gap: 10px; overflow-x: auto; }
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .filters { grid-template-columns: 1fr; }
            .cart-item { grid-template-columns: 70px 1fr auto; }
            .cart-item img { width: 70px; height: 90px; }
            .cart-price { grid-column: 2; }
            .thumbs { grid-template-columns: repeat(5, 1fr); }
            .footer-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 460px) {
            .site { width: min(100% - 24px, 1240px); }
            .hero { min-height: 470px; margin-top: 12px; }
            .hero-content { padding: 45px 25px; }
            .product-grid { grid-template-columns: 1fr 1fr; }
            .card-body { padding: 11px; }
            .card-body h3 { font-size: 15px; }
            .details h1 { font-size: 36px; }
            .cart-item { grid-template-columns: 64px 1fr; }
            .cart-item > * { grid-column: auto; }
            .cart-item .remove { grid-column: 2; }
        }
    </style>
</head>
<body>
    <div class="announcement">Cash on Delivery available across Pakistan · Free delivery on orders over {{ number_format(config('store.free_shipping_threshold')) }} {{ config('store.currency') }}</div>

    <header class="site header">
        <a class="logo" href="{{ route('home') }}">GOLDEN CLOTH HOUSE</a>

        <nav class="nav" aria-label="Main navigation">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('shop') }}">Shop</a>
            <a class="cart-link" href="{{ route('cart.index') }}">
                Cart
                <span class="cart-count">{{ array_sum(array_column(session('cart', []), 'quantity')) }}</span>
            </a>
        </nav>
    </header>

    <main class="site">
        @if (session('success'))
            <div class="success-box">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="site footer">
        <div class="footer-grid">
            <div>
                <div class="footer-title">{{ config('store.name') }}</div>
                <div>Quality clothing with convenient Cash on Delivery across Pakistan.</div>
            </div>
            <div>
                <div class="footer-title">Customer Care</div>
                <div class="footer-links">
                    <a href="{{ route('shop') }}">Shop</a>
                    <a href="{{ route('cart.index') }}">Cart</a>
                    <a href="{{ route('privacy') }}">Privacy Policy</a>
                    <a href="{{ route('terms') }}">Terms & Conditions</a>
                </div>
            </div>
            <div>
                <div class="footer-title">Contact</div>
                <div class="footer-links">
                    @if (config('store.contact_phone')) <a href="tel:{{ config('store.contact_phone') }}">{{ config('store.contact_phone') }}</a> @endif
                    @if (config('store.contact_email')) <a href="mailto:{{ config('store.contact_email') }}">{{ config('store.contact_email') }}</a> @endif
                    @if (config('store.whatsapp')) <a href="https://wa.me/{{ preg_replace('/\D+/', '', config('store.whatsapp')) }}">WhatsApp</a> @endif
                </div>
            </div>
        </div>
        <div style="margin-top:28px;">© {{ date('Y') }} {{ config('store.name') }}. All rights reserved.</div>
    </footer>

    @stack('scripts')
</body>
</html>
