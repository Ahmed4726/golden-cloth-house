<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CatalogImport;
use App\Models\Category;
use App\Models\HomepageSlide;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

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
        return view('catalog-imports.create', [
            'categories' => Category::where('active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'brands' => Brand::where('active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:204800'],
        ]);

        $file = $data['pdf'];

        if (! $file->isValid() || (int) $file->getSize() <= 0) {
            $message = 'The uploaded PDF is empty or the server could not receive the file correctly.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->withInput()->withErrors(['pdf' => $message]);
        }

        $path = $file->store('catalog-imports', 'public');

        if (! $path || ! Storage::disk('public')->exists($path)) {
            $message = 'The PDF could not be saved to storage. Check storage permissions and try again.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 500);
            }

            return back()->withInput()->withErrors(['pdf' => $message]);
        }

        $storedSize = (int) Storage::disk('public')->size($path);

        if ($storedSize <= 0) {
            Storage::disk('public')->delete($path);
            $message = 'The PDF was uploaded but the saved file is 0 bytes. Check the server PHP upload/storage limits and try again.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->withInput()->withErrors(['pdf' => $message]);
        }

        $import = CatalogImport::create([
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'pdf_path' => $path,
            'status' => 'uploaded',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'PDF uploaded successfully.',
                'import_id' => $import->id,
                'import_uuid' => $import->uuid,
                'name' => $import->name,
                'preset' => $import->presetData(),
                'finalize_url' => route('catalog-imports.finalize', $import),
            ]);
        }

        return redirect()->route('catalog-imports.builder', $import);
    }

    public function builder(CatalogImport $catalogImport)
    {
        return redirect()
            ->route('catalog-imports.create')
            ->with('info', 'For a reliable import, please re-select the original PDF. The new builder reads the PDF directly from your computer and does not stream it through Laravel.');
    }

    public function review(CatalogImport $catalogImport)
    {
        return view('catalog-imports.review', [
            'catalogImport' => $catalogImport->load([
                'products.categoryRelation',
                'products.brand',
                'generatedBrand',
                'generatedSlide',
            ]),
        ]);
    }

    public function publish(CatalogImport $catalogImport)
    {
        if (! in_array($catalogImport->status, ['completed', 'published'], true)) {
            return redirect()
                ->route('catalog-imports.review', $catalogImport)
                ->with('error', 'This catalog must be processed before it can be published.');
        }

        DB::transaction(function () use ($catalogImport) {
            $catalogImport = CatalogImport::whereKey($catalogImport->id)->lockForUpdate()->firstOrFail();

            if ($catalogImport->status === 'published') {
                return;
            }

            $catalogImport->products()->update(['active' => true]);
            $catalogImport->generatedBrand?->update(['active' => true]);
            $catalogImport->generatedSlide?->update(['active' => true]);
            $catalogImport->update(['status' => 'published']);
        });

        return redirect()
            ->route('catalog-imports.review', $catalogImport)
            ->with('success', 'Catalog products and generated homepage assets are now published.');
    }

    public function pdf(CatalogImport $catalogImport)
    {
        abort(410, 'The catalog PDF endpoint is no longer used. Re-select the original PDF in the catalog builder.');
    }

    public function storagePdf(string $filename)
    {
        abort(410, 'The catalog PDF endpoint is no longer used. Re-select the original PDF in the catalog builder.');
    }

    public function finalize(Request $request, CatalogImport $catalogImport): JsonResponse
    {
        if ($catalogImport->status !== 'uploaded') {
            return response()->json(['message' => 'This catalog has already been processed.'], 409);
        }

        $preset = $catalogImport->presetData();
        $data = $request->validate([
            'products' => ['required', 'array', 'min:1', 'max:100'],
            'products.*.name' => ['required', 'string', 'max:255'],
            'products.*.sku' => ['required', 'string', 'max:100', 'distinct:ignore_case', Rule::unique('products', 'sku')],
            'products.*.description' => ['nullable', 'string', 'max:10000'],
            'products.*.retail_price' => ['required', 'numeric', 'min:0'],
            'products.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'products.*.stock_quantity' => ['required', 'integer', 'min:0', 'max:100000'],
            'products.*.gender' => ['required', 'in:women,men'],
            'products.*.piece_count' => ['nullable', 'integer', 'min:1', 'max:10'],
            'products.*.included_pieces' => ['nullable', 'array', 'max:3'],
            'products.*.included_pieces.*' => ['string', 'in:kameez,dupatta,trouser'],
            'products.*.piece_lengths' => ['nullable', 'array:kameez,dupatta,trouser'],
            'products.*.piece_lengths.kameez' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'products.*.piece_lengths.dupatta' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'products.*.piece_lengths.trouser' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'products.*.fabric' => ['nullable', 'string', 'max:100'],
            'products.*.season' => ['nullable', 'string', 'max:50'],
            'products.*.design_code' => ['nullable', 'string', 'max:100'],
            'products.*.category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'products.*.brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'products.*.featured' => ['boolean'],
            'products.*.images' => ['required', 'array', 'min:1', 'max:20'],
            'products.*.images.*' => ['string', 'regex:/^data:image\/(jpeg|jpg|png|webp);base64,/'],
            'slider_image' => [
                Rule::requiredIf(fn (): bool => $request->filled('slider_title') || $request->filled('slider_link')),
                'nullable',
                'string',
                'regex:/^data:image\/(jpeg|jpg|png|webp);base64,/',
            ],
            'slider_title' => ['nullable', 'string', 'max:160'],
            'slider_link' => ['nullable', 'string', 'max:500', 'regex:~^(https?://[^\s]+|/(?!/)[^\s]*)$~i'],
        ]);

        $created = [];
        $alreadyCompleted = false;
        $generatedBrandId = null;
        $generatedSlideId = null;

        DB::transaction(function () use ($data, $catalogImport, $preset, &$created, &$alreadyCompleted, &$generatedBrandId, &$generatedSlideId) {
            $catalogImport = CatalogImport::whereKey($catalogImport->id)->lockForUpdate()->firstOrFail();

            if ($catalogImport->status !== 'uploaded') {
                $alreadyCompleted = true;

                return;
            }

            $collectionBrand = null;

            if ($preset) {
                $collectionBrand = Brand::where('name', $preset['brand_name'])->first();

                if (! $collectionBrand) {
                    $logoPath = $this->storeDataUrl(
                        $data['products'][0]['images'][0],
                        'brands',
                        Str::slug($preset['brand_name']).'-catalog'
                    );
                    $brandSlug = Str::slug($preset['brand_name']);
                    $slugSuffix = 2;

                    while (Brand::where('slug', $brandSlug)->exists()) {
                        $brandSlug = Str::slug($preset['brand_name']).'-'.$slugSuffix++;
                    }

                    $collectionBrand = Brand::create([
                        'name' => $preset['brand_name'],
                        'slug' => $brandSlug,
                        'description' => $preset['description'],
                        'logo_path' => $logoPath,
                        'active' => false,
                    ]);
                    $generatedBrandId = $collectionBrand->id;
                }
            }

            if (! empty($data['slider_image'])) {
                $slideImagePath = $this->storeDataUrl(
                    $data['slider_image'],
                    'slides',
                    Str::slug($data['slider_title'] ?? $catalogImport->name).'-catalog'
                );

                $slide = HomepageSlide::create([
                    'title' => filled($data['slider_title'] ?? null) ? trim($data['slider_title']) : $catalogImport->name,
                    'button_text' => filled($data['slider_link'] ?? null) ? 'Shop now' : null,
                    'button_url' => filled($data['slider_link'] ?? null) ? trim($data['slider_link']) : null,
                    'image_path' => $slideImagePath,
                    'sort_order' => (HomepageSlide::max('sort_order') ?? -1) + 1,
                    'active' => false,
                ]);
                $generatedSlideId = $slide->id;
            }

            $presetCategory = $preset
                ? Category::where('slug', $preset['category_slug'])->first()
                : null;

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
                    : $presetCategory;

                $retailPrice = (float) $item['retail_price'];
                $discount = (float) ($item['discount_percent'] ?? 3);
                $salePrice = round($retailPrice * (1 - ($discount / 100)), 2);
                $name = trim($item['name']);
                $includedPieces = array_values(array_unique($item['included_pieces'] ?? []));
                $pieceLengths = array_intersect_key(
                    $item['piece_lengths'] ?? [],
                    array_flip($includedPieces)
                );

                $product = Product::create([
                    'name' => $name,
                    'slug' => $this->uniqueSlug($name),
                    'sku' => $item['sku'],
                    'price' => $salePrice,
                    'currency' => 'PKR',
                    'stock_quantity' => $item['stock_quantity'],
                    'compare_at_price' => $retailPrice,
                    'discount_percent' => $discount,
                    'category' => $category?->name,
                    'category_id' => $category?->id,
                    'brand_id' => $collectionBrand?->id ?? ($item['brand_id'] ?? null),
                    'gender' => $item['gender'],
                    'wear_type' => 'unstitched',
                    'piece_count' => $includedPieces
                        ? count($includedPieces)
                        : ($item['piece_count'] ?? null),
                    'included_pieces' => $includedPieces,
                    'piece_lengths' => $pieceLengths,
                    'fabric' => $item['fabric'] ?? null,
                    'season' => $item['season'] ?? null,
                    'design_code' => $item['design_code'] ?? null,
                    'catalog_import_id' => $catalogImport->id,
                    'description' => $item['description'] ?? null,
                    'image_urls' => [],
                    'media_paths' => $mediaPaths,
                    'active' => false,
                    'featured' => (bool) ($item['featured'] ?? false),
                    'sort_order' => $index,
                ]);

                $created[] = $product->id;
            }

            $catalogImport->update([
                'product_count' => count($created),
                'generated_brand_id' => $generatedBrandId,
                'generated_slide_id' => $generatedSlideId,
                'status' => 'completed',
            ]);
        });

        if ($alreadyCompleted) {
            return response()->json(['message' => 'This catalog has already been processed.'], 409);
        }

        return response()->json([
            'message' => count($created).' draft products created. Review the catalog before publishing.',
            'redirect' => route('catalog-imports.review', $catalogImport),
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

        $slide = $catalogImport->generatedSlide;
        if ($slide) {
            Storage::disk('public')->delete($slide->image_path);
            $slide->delete();
        }

        $brand = $catalogImport->generatedBrand;
        if ($brand && ! $brand->products()->exists()) {
            Storage::disk('public')->delete($brand->logo_path);
            $brand->delete();
        }

        Storage::disk('public')->delete($catalogImport->pdf_path);
        $catalogImport->delete();

        return back()->with('success', 'Catalog import and its products were removed.');
    }

    private function storeDataUrl(string $dataUrl, string $directory, string $filename): string
    {
        [$meta, $encoded] = explode(',', $dataUrl, 2);
        $mime = str_contains($meta, 'image/webp')
            ? 'webp'
            : (str_contains($meta, 'image/png') ? 'png' : 'jpg');
        $binary = base64_decode($encoded, true);

        if ($binary === false || strlen($binary) < 100) {
            throw new RuntimeException('One of the selected PDF images is invalid.');
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
