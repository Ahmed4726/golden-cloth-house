<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index', [
            'products' => Product::orderByDesc('featured')
                ->orderBy('sort_order')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);
        $paths = $this->storeImages($request);

        Product::create([
            'name' => $data['name'],
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['name']),
            'sku' => ($data['sku'] ?? '') ?: null,
            'price' => $data['price'],
            'currency' => strtoupper($data['currency']),
            'stock_quantity' => $data['stock_quantity'],
            'compare_at_price' => $data['compare_at_price'] ?? null,
            'category' => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'image_urls' => $this->imageUrls($data['image_urls'] ?? ''),
            'media_paths' => $paths,
            'active' => $request->boolean('active', true),
            'featured' => $request->boolean('featured'),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product added successfully.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product);
        $paths = $product->media_paths ?? [];

        if ($request->hasFile('images')) {
            $paths = array_values(array_merge($paths, $this->storeImages($request)));
        }

        $product->update([
            'name' => $data['name'],
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['name']),
            'sku' => ($data['sku'] ?? '') ?: null,
            'price' => $data['price'],
            'currency' => strtoupper($data['currency']),
            'stock_quantity' => $data['stock_quantity'],
            'compare_at_price' => $data['compare_at_price'] ?? null,
            'category' => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'image_urls' => $this->imageUrls($data['image_urls'] ?? ''),
            'media_paths' => $paths,
            'active' => $request->boolean('active'),
            'featured' => $request->boolean('featured'),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->media_paths ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $product->delete();

        return back()->with('success', 'Product removed.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $productId = $product?->id;

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,'.$productId],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku,'.$productId],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:100000'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'category' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:10000'],
            'image_urls' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);
    }

    private function imageUrls(string $value): array
    {
        $urls = collect(preg_split('/\r?\n/', $value))
            ->map(fn ($url) => trim($url))
            ->filter()
            ->values()
            ->all();

        validator(
            ['image_urls' => $urls],
            [
                'image_urls' => ['array', 'max:20'],
                'image_urls.*' => ['url', 'max:2048'],
            ]
        )->validate();

        return $urls;
    }

    private function storeImages(Request $request): array
    {
        if (! $request->hasFile('images')) {
            return [];
        }

        Storage::disk('public')->makeDirectory('products');

        return collect($request->file('images'))
            ->filter(fn ($file) => $file->isValid())
            ->map(fn ($file) => $file->store('products', 'public'))
            ->values()
            ->all();
    }
}
