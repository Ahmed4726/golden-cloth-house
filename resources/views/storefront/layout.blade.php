<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $title ?? 'Golden Cloth House' }}</title>
<meta name="description" content="{{ $description ?? 'Golden Cloth House clothing collection.' }}">
<style>
:root{--ink:#17130f;--muted:#756d66;--paper:#faf8f4;--card:#fff;--line:#e8e1d8;--accent:#8b6a43}
*{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font:15px/1.6 Inter,system-ui,sans-serif}a{color:inherit;text-decoration:none}
.site{max-width:1240px;margin:auto;padding:0 24px}.header{height:78px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--line)}
.logo{font-weight:900;letter-spacing:.08em}.nav{display:flex;gap:24px}.hero{min-height:520px;display:flex;align-items:center;padding:70px 7%;background:#eee7df;margin:28px 0}.hero>div{max-width:680px}
.eyebrow{font-size:12px;font-weight:800;letter-spacing:.18em;color:var(--accent)}h1{font-size:clamp(42px,6vw,76px);line-height:1.02;margin:14px 0 20px}h2{font-size:34px;margin:6px 0}
.hero p,.page-head p{font-size:18px;color:var(--muted);max-width:600px}.shop-btn{display:inline-block;margin-top:18px;padding:13px 22px;background:var(--ink);color:white;border-radius:4px}
.section{padding:50px 0}.section-head{display:flex;justify-content:space-between;align-items:end;margin-bottom:24px}.product-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px}
.product-card{background:var(--card);border:1px solid var(--line)}.card-image{aspect-ratio:3/4;background:#eee7df;overflow:hidden}.card-image img{width:100%;height:100%;object-fit:cover}.placeholder{height:100%;display:grid;place-items:center;color:#9a8d80}
.card-body{padding:15px}.card-body h3{margin:5px 0 8px;font-size:17px}.muted,.sku{color:var(--muted)}.page-head{padding:70px 0 30px}
.product-detail{display:grid;grid-template-columns:1.1fr .9fr;gap:60px;padding:60px 0}.main-image{aspect-ratio:1/1;background:#eee7df;overflow:hidden}.main-image img{width:100%;height:100%;object-fit:cover}
.thumbs{display:flex;gap:10px;margin-top:10px;overflow:auto}.thumbs img{width:80px;height:80px;object-fit:cover}.details h1{font-size:54px}.price{font-size:25px;font-weight:800;margin:18px 0}.description{font-size:17px;color:#554e48;margin:25px 0}
.footer{border-top:1px solid var(--line);padding:30px 0;color:var(--muted);margin-top:50px}.empty{padding:50px;background:white;border:1px dashed var(--line)}
@media(max-width:900px){.product-grid{grid-template-columns:repeat(2,1fr)}.product-detail{grid-template-columns:1fr}.details h1{font-size:42px}}@media(max-width:560px){.site{padding:0 16px}.nav{gap:12px}.hero{margin:16px 0;padding:45px 25px}.product-grid{grid-template-columns:1fr 1fr;gap:12px}}
</style></head>
<body>
<header class="site header"><a class="logo" href="{{ route('home') }}">GOLDEN CLOTH HOUSE</a><nav class="nav"><a href="{{ route('home') }}">Home</a><a href="{{ route('shop') }}">Shop</a><a href="mailto:{{ config('mail.from.address') }}">Contact</a></nav></header>
<main class="site">@yield('content')</main>
<footer class="site footer">© {{ date('Y') }} Golden Cloth House. All rights reserved.</footer>
</body></html>