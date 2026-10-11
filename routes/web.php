<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogImportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomepageSlideController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SocialConnectionController;
use App\Http\Controllers\SocialPostController;
use App\Http\Controllers\SocialProviderController;
use App\Http\Controllers\StorefrontController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/shop', [StorefrontController::class, 'shop'])->name('shop');
Route::get('/collections/{category:slug}', [StorefrontController::class, 'collection'])->name('shop.collection');
Route::get('/shop/{product:slug}', [StorefrontController::class, 'product'])->name('shop.product');

Route::get('/about', fn () => view('storefront.about'))->name('about');
Route::get('/faq', fn () => view('storefront.faq'))->name('faq');
Route::get('/contact', fn () => view('storefront.contact'))->name('contact');
Route::view('/privacy', 'storefront.privacy')->name('privacy');
Route::view('/terms', 'storefront.terms')->name('terms');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/order/{order:order_number}/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/track-order', [OrderTrackingController::class, 'index'])->name('order.track');
Route::post('/track-order', [OrderTrackingController::class, 'show'])->name('order.track.submit');

Route::get('/media/{path}', [MediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.show');

Route::get('/robots.txt', function () {
    return response(
        "User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: ".url('/sitemap.xml'),
        200,
        ['Content-Type' => 'text/plain']
    );
})->name('robots');

Route::get('/sitemap.xml', function () {
    $products = Product::where('active', true)->get();
    $categories = Category::where('active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    return response()
        ->view('storefront.sitemap', compact('products', 'categories'))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

Route::get('/storage/catalog-imports/{filename}', [CatalogImportController::class, 'storagePdf'])
    ->where('filename', '[^/]+')
    ->name('catalog-imports.storage-pdf');

Route::middleware('admin.session')->prefix('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('/products', ProductController::class)->except(['show']);
    Route::delete('/products/{product}/images', [ProductController::class, 'removeImage'])->name('products.images.remove');
    Route::resource('/categories', CategoryController::class)->except(['show']);
    Route::resource('/brands', BrandController::class)->except(['show']);
    Route::resource('/slides', HomepageSlideController::class)->except(['show']);

    Route::get('/catalog-imports', [CatalogImportController::class, 'index'])->name('catalog-imports.index');
    Route::get('/catalog-imports/create', [CatalogImportController::class, 'create'])->name('catalog-imports.create');
    Route::post('/catalog-imports', [CatalogImportController::class, 'store'])->name('catalog-imports.store');
    Route::get('/catalog-imports/{catalogImport}/review', [CatalogImportController::class, 'review'])->name('catalog-imports.review');
    Route::post('/catalog-imports/{catalogImport}/publish', [CatalogImportController::class, 'publish'])->name('catalog-imports.publish');
    Route::get('/catalog-imports/{catalogImport}/pdf', [CatalogImportController::class, 'pdf'])->name('catalog-imports.pdf');
    Route::get('/catalog-imports/{catalogImport}/builder', [CatalogImportController::class, 'builder'])->name('catalog-imports.builder');
    Route::post('/catalog-imports/{catalogImport}/finalize', [CatalogImportController::class, 'finalize'])->name('catalog-imports.finalize');
    Route::delete('/catalog-imports/{catalogImport}', [CatalogImportController::class, 'destroy'])->name('catalog-imports.destroy');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');

    Route::get('/connections', [SocialConnectionController::class, 'index'])->name('connections.index');
    Route::get('/connections/{provider}/oauth', [SocialConnectionController::class, 'oauth'])->name('social.oauth');
    Route::get('/connections/{provider}/callback', [SocialConnectionController::class, 'callback'])->name('social.oauth.callback');
    Route::post('/connections/{connection}/toggle', [SocialConnectionController::class, 'toggle'])->name('connections.toggle');
    Route::delete('/connections/{connection}', [SocialConnectionController::class, 'destroy'])->name('connections.destroy');

    Route::get('/posts', [SocialPostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [SocialPostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [SocialPostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [SocialPostController::class, 'show'])->name('posts.show');
    Route::post('/posts/{post}/publish', [SocialPostController::class, 'publish'])->name('posts.publish');
    Route::get('/api/readiness', [SocialProviderController::class, 'readiness'])->name('social.readiness');
});
