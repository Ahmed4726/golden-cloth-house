<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index', [
            'products' => Product::with(['categoryRelation', 'brand'])
                ->orderByDesc('featured')->orderBy('sort_order')->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('products.create', [
            'categories' => Category::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'brands' => Brand::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);
        $paths = $this->storeImages($request);
        $category = ! empty($data['category_id']) ? Category::find($data['category_id']) : null;

        Product::create($this->productData($request, $data, $paths, $category));

        return redirect()->route('products.index')->with('success', 'Product added successfully.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'product' => $product->load(['categoryRelation', 'brand']),
            'categories' => Category::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'brands' => Brand::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product);
        $paths = $product->media_paths ?? [];
        $category = ! empty($data['category_id']) ? Category::find($data['category_id']) : null;

        if ($request->hasFile('images')) {
            $paths = array_values(array_merge($paths, $this->storeImages($request)));
        }

        $product->update($this->productData($request, $data, $paths, $category, $product));

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function removeImage(Request $request, Product $product)
    {
        $data = $request->validate([
            'type' => ['required', 'in:media,url'],
            'value' => ['required', 'string', 'max:2048'],
        ]);

        if ($data['type'] === 'media') {
            $paths = array_values(array_filter(
                $product->media_paths ?? [],
                fn (string $path) => $path !== $data['value']
            ));

            if (in_array($data['value'], $product->media_paths ?? [], true)) {
                Storage::disk('public')->delete($data['value']);
            }

            $product->update(['media_paths' => $paths]);
        } else {
            $urls = array_values(array_filter(
                $product->image_urls ?? [],
                fn (string $url) => $url !== $data['value']
            ));

            $product->update(['image_urls' => $urls]);
        }

        return back()->with('success', 'Image removed successfully.');
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
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product?->id)],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product?->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:100000'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'gender' => ['nullable', 'in:women,men'],
            'wear_type' => ['nullable', 'string', 'max:30'],
            'piece_count' => ['nullable', 'integer', 'min:1', 'max:10'],
            'included_pieces' => ['nullable', 'array', 'max:3'],
            'included_pieces.*' => ['string', 'in:kameez,dupatta,trouser', 'distinct'],
            'piece_lengths' => ['nullable', 'array:kameez,dupatta,trouser'],
            'piece_lengths.kameez' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'piece_lengths.dupatta' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'piece_lengths.trouser' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'fabric' => ['nullable', 'string', 'max:100'],
            'season' => ['nullable', 'string', 'max:50'],
            'design_code' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:10000'],
            'image_urls' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);
    }

    private function productData(
        Request $request,
        array $data,
        array $paths,
        ?Category $category,
        ?Product $product = null
    ): array {
        $includedPieces = array_values(array_unique($data['included_pieces'] ?? []));
        $pieceLengths = array_intersect_key(
            $data['piece_lengths'] ?? [],
            array_flip($includedPieces)
        );

        return [
            'name' => $data['name'],
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['name']),
            'sku' => ($data['sku'] ?? '') ?: null,
            'price' => $data['price'],
            'currency' => strtoupper($data['currency']),
            'stock_quantity' => $data['stock_quantity'],
            'compare_at_price' => $data['compare_at_price'] ?? null,
            'discount_percent' => $data['discount_percent'] ?? 0,
            'category' => $category?->name ?: ($data['category'] ?? null),
            'category_id' => $data['category_id'] ?? null,
            'brand_id' => $data['brand_id'] ?? null,
            'gender' => $data['gender'] ?? null,
            'wear_type' => $data['wear_type'] ?? 'unstitched',
            'piece_count' => $includedPieces
                ? count($includedPieces)
                : ($data['piece_count'] ?? null),
            'included_pieces' => $includedPieces,
            'piece_lengths' => $pieceLengths,
            'fabric' => $data['fabric'] ?? null,
            'season' => $data['season'] ?? null,
            'design_code' => $data['design_code'] ?? null,
            'description' => $data['description'] ?? null,
            'image_urls' => $this->imageUrls($data['image_urls'] ?? ''),
            'media_paths' => $paths,
            'active' => $request->boolean('active', $product?->active ?? true),
            'featured' => $request->boolean('featured'),
            'sort_order' => $data['sort_order'] ?? 0,
        ];
    }

    private function imageUrls(string $value): array
    {
        $urls = collect(preg_split('/\r?\n/', $value))
            ->map(fn ($url) => trim($url))
            ->filter()
            ->values()
            ->all();

        validator([
            'image_urls' => $urls,
        ], [
            'image_urls' => ['array', 'max:20'],
            'image_urls.*' => ['url', 'max:2048'],
        ])->validate();

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
