<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Golden Cloth House' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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
                    <a class="nav-link text-white {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"><i class="bi bi-shop me-2"></i>View Shop</a>
                    <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a class="nav-link text-white {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}"><i class="bi bi-box-seam me-2"></i>Products</a>
                    <a class="nav-link text-white {{ request()->routeIs('catalog-imports.*') ? 'active' : '' }}" href="{{ route('catalog-imports.index') }}"><i class="bi bi-file-earmark-pdf me-2"></i>PDF Catalogs</a>
                    <a class="nav-link text-white {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="bi bi-tags me-2"></i>Categories</a>
                    <a class="nav-link text-white {{ request()->routeIs('brands.*') ? 'active' : '' }}" href="{{ route('brands.index') }}"><i class="bi bi-award me-2"></i>Brands</a>
                    <a class="nav-link text-white {{ request()->routeIs('slides.*') ? 'active' : '' }}" href="{{ route('slides.index') }}"><i class="bi bi-images me-2"></i>Homepage Slider</a>
                    <a class="nav-link text-white {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}"><i class="bi bi-bag-check me-2"></i>Orders</a>
                    <a class="nav-link text-white {{ request()->routeIs('connections.*') ? 'active' : '' }}" href="{{ route('connections.index') }}"><i class="bi bi-share me-2"></i>Social Connections</a>
                    <a class="nav-link text-white {{ request()->routeIs('posts.*') ? 'active' : '' }}" href="{{ route('posts.index') }}"><i class="bi bi-send me-2"></i>Social Posts</a>
                    <a class="nav-link text-white {{ request()->routeIs('posts.create') ? 'active' : '' }}" href="{{ route('posts.create') }}"><i class="bi bi-plus-circle me-2"></i>Create Post</a>
                </nav>
                <div class="mt-auto pt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light w-100">
                            <i class="bi bi-box-arrow-right me-2"></i>Sign out
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-grow-1">
            <div class="container-fluid px-3 px-md-4 py-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
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
