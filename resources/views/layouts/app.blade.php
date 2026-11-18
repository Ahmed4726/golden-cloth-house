<!doctype html>
<html lang="en">
<head>
<script async src="https://www.googletagmanager.com/gtm.js?id=GTM-P6ZTCG4X"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Golden Cloth House' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .ratio-2x1 { --bs-aspect-ratio: 50%; }
        .ratio-3x4 { --bs-aspect-ratio: 133.333333%; }
        .ratio-4x5 { --bs-aspect-ratio: 125%; }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark d-lg-none sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">Golden Cloth House</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMenu" aria-controls="adminMenu" aria-label="Open admin menu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="d-flex min-vh-100">
        <aside class="offcanvas-lg offcanvas-start col-lg-3 col-xl-2 text-bg-dark flex-shrink-0" tabindex="-1" id="adminMenu" aria-labelledby="adminMenuLabel">
            <div class="offcanvas-header d-lg-none border-bottom border-secondary">
                <h5 class="offcanvas-title fw-bold" id="adminMenuLabel">Golden Cloth House</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#adminMenu" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column p-3">
                <div class="fs-5 fw-bold px-2 py-2 mb-3 d-none d-lg-block">Golden Cloth House</div>
                <nav class="nav nav-pills flex-column gap-1">
                    <a class="nav-link text-white {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"><i class="fa-solid fa-store me-2"></i>View Shop</a>
                    <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge-high me-2"></i>Dashboard</a>
                    <a class="nav-link text-white {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.edit') }}"><i class="fa-solid fa-gear me-2"></i>Settings</a>
                    <a class="nav-link text-white {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}"><i class="fa-solid fa-box me-2"></i>Products</a>
                    <a class="nav-link text-white {{ request()->routeIs('catalog-imports.*') ? 'active' : '' }}" href="{{ route('catalog-imports.index') }}"><i class="fa-solid fa-file-pdf me-2"></i>PDF Catalogs</a>
                    <a class="nav-link text-white {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="fa-solid fa-tags me-2"></i>Categories</a>
                    <a class="nav-link text-white {{ request()->routeIs('brands.*') ? 'active' : '' }}" href="{{ route('brands.index') }}"><i class="fa-solid fa-award me-2"></i>Brands</a>
                    <a class="nav-link text-white {{ request()->routeIs('fabrics.*') ? 'active' : '' }}" href="{{ route('fabrics.index') }}"><i class="fa-solid fa-layer-group me-2"></i>Fabrics</a>
                    <a class="nav-link text-white {{ request()->routeIs('coupons.*') ? 'active' : '' }}" href="{{ route('coupons.index') }}"><i class="fa-solid fa-ticket me-2"></i>Coupons</a>
                    <a class="nav-link text-white {{ request()->routeIs('slides.*') ? 'active' : '' }}" href="{{ route('slides.index') }}"><i class="fa-solid fa-images me-2"></i>Homepage Slider</a>
                    <a class="nav-link text-white {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}"><i class="fa-solid fa-bag-shopping me-2"></i>Orders</a>
                    <a class="nav-link text-white {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><i class="fa-solid fa-users me-2"></i>Customers</a>
                    <a class="nav-link text-white {{ request()->routeIs('connections.*') ? 'active' : '' }}" href="{{ route('connections.index') }}"><i class="fa-solid fa-share-nodes me-2"></i>Social Connections</a>
                    <a class="nav-link text-white {{ request()->routeIs('posts.*') ? 'active' : '' }}" href="{{ route('posts.index') }}"><i class="fa-solid fa-paper-plane me-2"></i>Social Posts</a>
                    <a class="nav-link text-white {{ request()->routeIs('posts.create') ? 'active' : '' }}" href="{{ route('posts.create') }}"><i class="fa-solid fa-circle-plus me-2"></i>Create Post</a>
                </nav>
                <div class="mt-auto pt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light w-100">
                            <i class="fa-solid fa-right-from-bracket me-2"></i>Sign out
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-grow-1 overflow-hidden">
            <div class="container-fluid px-3 px-md-4 py-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
