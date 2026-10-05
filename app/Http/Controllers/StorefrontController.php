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
            'categories' => $this->categories(),
        ]);
    }

    public function shop(Request $request)
    {
        $products = Product::where('active', true)
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->input('q'));

                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('category', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', (string) $request->input('category'));
            })
            ->when($request->filled('min_price'), function ($query) use ($request) {
                $query->where('price', '>=', (float) $request->input('min_price'));
            })
            ->when($request->filled('max_price'), function ($query) use ($request) {
                $query->where('price', '<=', (float) $request->input('max_price'));
            })
            ->when($request->boolean('in_stock'), function ($query) {
                $query->where('stock_quantity', '>', 0);
            });

        $sort = (string) $request->input('sort', 'featured');

        match ($sort) {
            'price_low' => $products->orderBy('price')->orderBy('name'),
            'price_high' => $products->orderByDesc('price')->orderBy('name'),
            'name_az' => $products->orderBy('name'),
            'newest' => $products->latest(),
            default => $products->orderByDesc('featured')->orderBy('sort_order')->latest(),
        };

        $products = $products->paginate(24)->withQueryString();

        return view('storefront.shop', [
            'products' => $products,
            'categories' => $this->categories(),
            'filter' => [
                'q' => (string) $request->input('q', ''),
                'category' => (string) $request->input('category', ''),
                'min_price' => $request->input('min_price', ''),
                'max_price' => $request->input('max_price', ''),
                'sort' => $sort,
                'in_stock' => $request->boolean('in_stock'),
            ],
        ]);
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

    private function categories()
    {
        return Product::where('active', true)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }
}
