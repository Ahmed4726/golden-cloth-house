<?php

namespace App\Http\Controllers;

use App\Models\HomepageSlide;
use App\Models\Product;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function home()
    {
        return view('storefront.home', [
            'slides' => HomepageSlide::where('active', true)->orderBy('sort_order')->get(),
            'products' => Product::where('active', true)
                ->where('stock_quantity', '>', 0)
                ->orderByDesc('featured')
                ->orderBy('sort_order')
                ->latest()
                ->take(12)
                ->get(),
            'categories' => Product::where('active', true)
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
        ]);
    }

    public function shop(Request $request)
    {
        $products = Product::where('active', true)
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim($request->string('q'));
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('category', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->latest()
            ->paginate(24)
            ->withQueryString();

        $categories = Product::where('active', true)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('storefront.shop', compact('products', 'categories'));
    }

    public function product(Product $product)
    {
        abort_unless($product->active, 404);

        $related = Product::where('active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category, fn ($query) => $query->where('category', $product->category))
            ->where('stock_quantity', '>', 0)
            ->latest()
            ->take(4)
            ->get();

        return view('storefront.product', compact('product', 'related'));
    }
}
