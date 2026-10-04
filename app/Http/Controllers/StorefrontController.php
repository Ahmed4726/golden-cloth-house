<?php

namespace App\Http\Controllers;

use App\Models\Product;

class StorefrontController extends Controller
{
    public function home()
    {
        return view('storefront.home', [
            'products' => Product::where('active', true)->latest()->take(12)->get(),
        ]);
    }

    public function shop()
    {
        return view('storefront.shop', [
            'products' => Product::where('active', true)->latest()->paginate(24),
        ]);
    }

    public function product(Product $product)
    {
        abort_unless($product->active, 404);

        return view('storefront.product', compact('product'));
    }
}
