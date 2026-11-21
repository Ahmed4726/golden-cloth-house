<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\HomepageSlide;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function home()
    {
        $categories = Category::where('active', true)
            ->withCount(['products as active_products_count' => fn ($query) => $query->where('active', true)])
            ->with(['products' => fn ($query) => $query
                ->where('active', true)
                ->where('stock_quantity', '>', 0)
                ->latest()
                ->limit(1)])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categoryHighlights = $categories
            ->filter(fn ($category) => $category->active_products_count > 0)
            ->take(12)
            ->map(fn ($category) => [
                'category' => $category,
                'product' => $category->products->first(),
            ]);

        return view('storefront.home', [
            'slides' => HomepageSlide::where('active', true)->orderBy('sort_order')->get(),
            'products' => Product::where('active', true)
                ->where('stock_quantity', '>', 0)
                ->orderByDesc('featured')
                ->orderBy('sort_order')
                ->latest()
                ->take(12)
                ->get(),
            'featuredProducts' => Product::where('active', true)
                ->where('stock_quantity', '>', 0)
                ->where('featured', true)
                ->orderBy('sort_order')
                ->latest()
                ->take(8)
                ->get(),
            'saleProducts' => Product::where('active', true)
                ->where('stock_quantity', '>', 0)
                ->whereNotNull('compare_at_price')
                ->whereColumn('compare_at_price', '>', 'price')
                ->latest()
                ->take(8)
                ->get(),
            'brands' => Brand::where('active', true)
                ->whereHas('products', fn ($query) => $query
                    ->where('active', true)
                    ->where('stock_quantity', '>', 0))
                ->with(['products' => fn ($query) => $query
                    ->where('active', true)
                    ->where('stock_quantity', '>', 0)
                    ->orderByDesc('featured')
                    ->orderBy('sort_order')
                    ->latest()
                    ->limit(4)])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'categories' => $categories,
            'categoryHighlights' => $categoryHighlights,
        ]);
    }

    public function shop(Request $request)
    {
        $products = Product::with(['categoryRelation', 'brand'])
            ->where('active', true)
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->input('q'));

                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('category', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhereHas('brand', fn ($brand) => $brand->where('name', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $category = (string) $request->input('category');

                $query->where(function ($q) use ($category) {
                    if (ctype_digit($category)) {
                        $q->where('category_id', (int) $category);
                    } else {
                        $q->whereHas('categoryRelation', function ($categoryQuery) use ($category) {
                            $categoryQuery->where('slug', $category)
                                ->orWhere('name', $category);
                        })->orWhere('category', $category);
                    }
                });
            })
            ->when($request->filled('brand'), fn ($query) => $query->where('brand_id', $request->integer('brand')))
            ->when($request->filled('min_price'), fn ($query) => $query->where('price', '>=', (float) $request->input('min_price')))
            ->when($request->filled('max_price'), fn ($query) => $query->where('price', '<=', (float) $request->input('max_price')))
            ->when($request->boolean('in_stock'), fn ($query) => $query->where('stock_quantity', '>', 0))
            ->when($request->boolean('sale'), fn ($query) => $query->whereColumn('compare_at_price', '>', 'price'));

        $sort = (string) $request->input('sort', 'featured');

        match ($sort) {
            'price_low' => $products->orderBy('price')->orderBy('name'),
            'price_high' => $products->orderByDesc('price')->orderBy('name'),
            'name_az' => $products->orderBy('name'),
            'newest' => $products->latest(),
            default => $products->orderByDesc('featured')->orderBy('sort_order')->latest(),
        };

        $collection = $request->routeIs('shop.collection')
            ? $request->route('category')
            : null;
        $brandCollection = $request->routeIs('shop.brand')
            ? $request->route('brand')
            : null;

        $dailyFeaturedCollection = null;

        if (! $collection && ! $brandCollection) {
            $featuredImages = Product::query()
                ->where('active', true)
                ->where('stock_quantity', '>', 0)
                ->where(function ($query) {
                    $query->whereJsonLength('media_paths', '>', 0)
                        ->orWhereJsonLength('image_urls', '>', 0);
                })
                ->with('categoryRelation:id,name')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(30)
                ->get()
                ->flatMap(fn (Product $product) => collect($product->images())
                    ->map(fn (string $image) => [
                        'product' => $product,
                        'image' => $product->imageUrl($image, 960),
                        'image_srcset' => implode(', ', [
                            $product->imageUrl($image, 480).' 480w',
                            $product->imageUrl($image, 640).' 640w',
                            $product->imageUrl($image, 960).' 960w',
                            $product->imageUrl($image, 1280).' 1280w',
                        ]),
                    ]))
                ->unique('image')
                ->values();

            if ($featuredImages->isNotEmpty()) {
                $today = now()->startOfDay();
                $dayNumber = intdiv($today->getTimestamp() + $today->getOffset(), 86400);
                $dailyIndex = $dayNumber % $featuredImages->count();
                $dailyFeaturedCollection = $featuredImages->get($dailyIndex);
            }
        }

        return view('storefront.shop', [
            'products' => $products->paginate(24)->withQueryString(),
            'categories' => Category::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'brands' => Brand::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'heroSlide' => HomepageSlide::where('active', true)->orderBy('sort_order')->first(),
            'collection' => $collection,
            'brandCollection' => $brandCollection,
            'dailyFeaturedCollection' => $dailyFeaturedCollection,
            'canonical' => $collection
                ? route('shop.collection', $collection->slug)
                : ($brandCollection ? route('shop.brand', $brandCollection->slug) : route('shop')),
            'filter' => [
                'q' => (string) $request->input('q', ''),
                'category' => (string) $request->input('category', ''),
                'brand' => (string) $request->input('brand', ''),
                'min_price' => $request->input('min_price', ''),
                'max_price' => $request->input('max_price', ''),
                'sort' => $sort,
                'in_stock' => $request->boolean('in_stock'),
                'sale' => $request->boolean('sale'),
            ],
        ]);
    }

    public function collection(Category $category, Request $request)
    {
        abort_unless($category->active, 404);

        $request->query->set('category', $category->slug);

        return $this->shop($request);
    }

    public function brand(Brand $brand, Request $request): View
    {
        abort_unless($brand->active, 404);

        $request->query->set('brand', (string) $brand->id);

        return $this->shop($request);
    }

    public function product(Product $product)
    {
        abort_unless($product->active, 404);
        $product->load(['kameezFabric', 'trouserFabric', 'dupattaFabric']);

        $related = Product::where('active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($query) => $query->where('category_id', $product->category_id))
            ->where('stock_quantity', '>', 0)
            ->latest()
            ->take(4)
            ->get();

        return view('storefront.product', compact('product', 'related'));
    }
}
