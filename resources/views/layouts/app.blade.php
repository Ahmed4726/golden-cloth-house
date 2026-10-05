<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Golden Cloth House' }}</title>

    <style>
        :root {
            --bg: #f6f7fb;
            --card: #ffffff;
            --ink: #171923;
            --muted: #697386;
            --line: #e7e9ef;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font: 15px/1.5 Inter, ui-sans-serif, system-ui, sans-serif;
        }

        a { text-decoration: none; color: inherit; }
        button, input, textarea, select { font: inherit; }

        .shell { display: flex; min-height: 100vh; }

        .side {
            width: 250px;
            background: #111827;
            color: #fff;
            padding: 24px 16px;
            position: fixed;
            inset: 0 auto 0 0;
            overflow-y: auto;
        }

        .brand {
            font-size: 20px;
            font-weight: 800;
            padding: 8px 12px 28px;
        }

        .nav a {
            display: block;
            padding: 11px 12px;
            border-radius: 10px;
            color: #cbd5e1;
            margin: 4px 0;
        }

        .nav a:hover,
        .nav a.active {
            background: #1f2937;
            color: #fff;
        }

        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 28px;
            max-width: 1500px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 15px;
        }

        .top h1 { margin: 0; font-size: 28px; }
        .muted { color: var(--muted); }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 8px #11182708;
        }

        .stat { font-size: 30px; font-weight: 800; margin-top: 5px; }

        .btn {
            border: 0;
            border-radius: 10px;
            padding: 10px 15px;
            background: #111827;
            color: #fff;
            cursor: pointer;
            display: inline-block;
        }

        .btn.secondary {
            background: #fff;
            color: #111827;
            border: 1px solid var(--line);
        }

        .btn.danger { background: #dc2626; }
        .btn:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible { outline: 3px solid #93c5fd; outline-offset: 2px; }

        .row { display: flex; gap: 10px; align-items: center; }

        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { text-align: left; padding: 13px 10px; border-bottom: 1px solid var(--line); vertical-align: top; }

        .badge {
            display: inline-flex;
            padding: 4px 9px;
            border-radius: 999px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 12px;
            font-weight: 700;
        }

        .badge.ok { background: #ecfdf5; color: #047857; }
        .badge.bad { background: #fef2f2; color: #b91c1c; }

        .form { max-width: 900px; }
        .field { margin-bottom: 16px; }
        .field label { display: block; font-weight: 700; margin-bottom: 6px; }
        .field input, .field textarea, .field select { width: 100%; padding: 11px 12px; border: 1px solid #d7dae2; border-radius: 10px; background: #fff; }
        .field textarea { min-height: 130px; }

        .checkgrid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .check { border: 1px solid var(--line); padding: 12px; border-radius: 10px; }

        .flash, .error {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 18px;
        }

        .flash { background: #ecfdf5; color: #065f46; }
        .error { background: #fef2f2; color: #991b1b; }

        code { background: #f3f4f6; padding: 2px 5px; border-radius: 4px; }

        @media (max-width: 1000px) {
            .side { width: 210px; }
            .main { margin-left: 210px; width: calc(100% - 210px); padding: 18px; }
            .grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 700px) {
            .side { position: static; width: 100%; height: auto; }
            .shell { display: block; }
            .main { margin-left: 0; width: 100%; padding: 15px; }
            .grid, .checkgrid { grid-template-columns: 1fr; }
            .top { align-items: flex-start; flex-direction: column; }
            .row { flex-wrap: wrap; }
            .table { display: block; overflow-x: auto; white-space: nowrap; }
        }
    </style>
</head>

<body>
    <div class="shell">
        <aside class="side">
            <div class="brand">Golden Cloth House</div>

            <nav class="nav">
                <a href="{{ route('home') }}">View Shop</a>
                <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a>
                <a class="{{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">Orders</a>
                <a class="{{ request()->routeIs('connections.*') ? 'active' : '' }}" href="{{ route('connections.index') }}">Social Connections</a>
                <a class="{{ request()->routeIs('posts.*') ? 'active' : '' }}" href="{{ route('posts.index') }}">Social Posts</a>
                <a href="{{ route('posts.create') }}">+ Create Post</a>
            </nav>

            <form method="POST" action="{{ route('logout') }}" style="margin-top:30px;">
                @csrf
                <button type="submit" class="btn secondary" style="width:100%;">Sign out</button>
            </form>
        </aside>

        <main class="main">
            @if (session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
