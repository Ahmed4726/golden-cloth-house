<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CatalogImport;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogImportController extends Controller
{
    public function index()
    {
        return view('catalog-imports.index', [
            'imports' => CatalogImport::withCount('products')->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('catalog-imports.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        $file = $data['pdf'];
        $path = $file->store('catalog-imports', 'public');

        $import = CatalogImport::create([
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'pdf_path' => $path,
            'status' => 'uploaded',
        ]);

        return redirect()->route('catalog-imports.builder', $import);
    }

    public function builder(CatalogImport $catalogImport)
    {
        return view('catalog-imports.builder', [
            'catalogImport' => $catalogImport,
            'categories' => Category::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'brands' => Brand::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function pdf(CatalogImport $catalogImport)
    {
        $disk = Storage::disk('public');

        abort_unless($disk->exists($catalogImport->pdf_path), 404, 'The catalog PDF could not be found.');

        return response()->file(
            $disk->path($catalogImport->pdf_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.basename($catalogImport->pdf_path).'"',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
            ]
        );
    }

    public function storagePdf(string $filename)
    {
        $catalogImport = CatalogImport::where('pdf_path', 'catalog-imports/'.$filename)->firstOrFail();

        return $this->pdf($catalogImport);
    }

    public function finalize(Request $request, CatalogImport $catalogImport): JsonResponse
    {
        $data = $request->validate([
            'products' => ['required', 'array', 'min:1', 'max:100'],
            'products.*.name' => ['required', 'string', 'max:255'],
            'products.*.description' => ['nullable', 'string', 'max:10000'],
            'products.*.retail_price' => ['required', 'numeric', 'min:0'],
            'products.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'products.*.stock_quantity' => ['required', 'integer', 'min:0', 'max:100000'],
            'products.*.gender' => ['required', 'in:women,men'],
            'products.*.piece_count' => ['nullable', 'integer', 'min:1', 'max:10'],
            'products.*.fabric' => ['nullable', 'string', 'max:100'],
            'products.*.season' => ['nullable', 'string', 'max:50'],
            'products.*.design_code' => ['nullable', 'string', 'max:100'],
            'products.*.category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'products.*.brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'products.*.featured' => ['boolean'],
            'products.*.images' => ['required', 'array', 'min:1', 'max:20'],
            'products.*.images.*' => ['string', 'regex:/^data:image\/(jpeg|jpg|png|webp);base64,/'],
        ]);

        $created = [];

        DB::transaction(function () use ($data, $catalogImport, &$created) {
            foreach ($data['products'] as $index => $item) {
                $mediaPaths = [];

                foreach ($item['images'] as $imageIndex => $dataUrl) {
                    $mediaPaths[] = $this->storeDataUrl(
                        $dataUrl,
                        'products/catalog/'.$catalogImport->uuid,
                        'design-'.($index + 1).'-'.($imageIndex + 1)
                    );
                }

                $category = ! empty($item['category_id'])
                    ? Category::find($item['category_id'])
                    : null;

                $retailPrice = (float) $item['retail_price'];
                $discount = (float) ($item['discount_percent'] ?? 3);
                $salePrice = round($retailPrice * (1 - ($discount / 100)), 2);
                $name = trim($item['name']);

                $product = Product::create([
                    'name' => $name,
                    'slug' => $this->uniqueSlug($name),
                    'sku' => null,
                    'price' => $salePrice,
                    'currency' => 'PKR',
                    'stock_quantity' => $item['stock_quantity'],
                    'compare_at_price' => $retailPrice,
                    'discount_percent' => $discount,
                    'category' => $category?->name,
                    'category_id' => $item['category_id'] ?? null,
                    'brand_id' => $item['brand_id'] ?? null,
                    'gender' => $item['gender'],
                    'wear_type' => 'unstitched',
                    'piece_count' => $item['piece_count'] ?? null,
                    'fabric' => $item['fabric'] ?? null,
                    'season' => $item['season'] ?? null,
                    'design_code' => $item['design_code'] ?? null,
                    'catalog_import_id' => $catalogImport->id,
                    'description' => $item['description'] ?? null,
                    'image_urls' => [],
                    'media_paths' => $mediaPaths,
                    'active' => true,
                    'featured' => (bool) ($item['featured'] ?? false),
                    'sort_order' => $index,
                ]);

                $created[] = $product->id;
            }

            $catalogImport->update([
                'product_count' => count($created),
                'status' => 'completed',
            ]);
        });

        return response()->json([
            'message' => count($created).' products created successfully.',
            'redirect' => route('products.index'),
        ]);
    }

    public function destroy(CatalogImport $catalogImport)
    {
        foreach ($catalogImport->products as $product) {
            foreach ($product->media_paths ?? [] as $path) {
                Storage::disk('public')->delete($path);
            }
            $product->delete();
        }

        Storage::disk('public')->delete($catalogImport->pdf_path);
        $catalogImport->delete();

        return back()->with('success', 'Catalog import and its products were removed.');
    }

    private function storeDataUrl(string $dataUrl, string $directory, string $filename): string
    {
        [$meta, $encoded] = explode(',', $dataUrl, 2);
        $mime = str_contains($meta, 'image/webp') ? 'webp' : (str_contains($meta, 'image/png') ? 'png' : 'jpg');
        $binary = base64_decode($encoded, true);

        if ($binary === false || strlen($binary) < 100) {
            abort(422, 'One of the selected PDF images is invalid.');
        }

        $path = $directory.'/'.$filename.'-'.Str::lower(Str::random(8)).'.'.$mime;
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $counter = 2;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
