<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\CatalogImport;
use App\Models\Category;
use App\Models\Fabric;
use App\Models\HomepageSlide;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_a_catalog_pdf(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->withSession(['admin_user_id' => $user->id])
            ->post('/admin/catalog-imports', [
                'pdf' => UploadedFile::fake()->createWithContent(
                    'LIME-VOL-552.pdf',
                    "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\n%%EOF"
                ),
            ])
            ->assertRedirect();

        $import = CatalogImport::firstOrFail();
        Storage::disk('public')->assertExists($import->pdf_path);
    }

    public function test_admin_can_create_a_product_from_selected_pdf_images_with_three_percent_discount(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Women Unstitched',
            'slug' => 'women-unstitched',
            'active' => true,
        ]);
        $import = CatalogImport::create([
            'name' => 'LIME VOL-552',
            'pdf_path' => 'catalog-imports/test.pdf',
            'status' => 'uploaded',
        ]);

        $png = 'data:image/png;base64,'.base64_encode(str_repeat('a', 128));
        $payload = [
            'products' => [[
                'name' => 'Lime Vol-552 — Design 01',
                'sku' => 'LIME-552-DESIGN-01',
                'description' => 'Peach Slub Embroidered with Peach Slub Embroidered Dupatta.',
                'retail_price' => 6200,
                'discount_percent' => 3,
                'stock_quantity' => 3,
                'gender' => 'women',
                'piece_count' => 3,
                'included_pieces' => ['kameez', 'dupatta', 'trouser'],
                'piece_lengths' => [
                    'kameez' => 40,
                    'dupatta' => 2.5,
                    'trouser' => 39,
                ],
                'fabric' => 'Peach Slub',
                'season' => 'Winter',
                'design_code' => 'DES-01',
                'images' => [$png],
            ]],
            'slider_image' => $png,
            'slider_title' => 'Lime collection',
            'slider_link' => '/shop',
        ];

        $this->withSession(['admin_user_id' => $user->id])
            ->postJson('/admin/catalog-imports/'.$import->id.'/finalize', [
                ...$payload,
            ])
            ->assertOk()
            ->assertJsonPath('message', '1 draft products created. Review the catalog before publishing.')
            ->assertJsonPath('redirect', route('catalog-imports.review', $import));

        $this->assertDatabaseHas('products', [
            'name' => 'Lime Vol-552 — Design 01',
            'price' => 6014,
            'compare_at_price' => 6200,
            'discount_percent' => 3,
            'gender' => 'women',
            'wear_type' => 'unstitched',
            'piece_count' => 3,
            'category_id' => $category->id,
            'catalog_import_id' => $import->id,
            'active' => false,
        ]);

        $product = $import->products()->firstOrFail();
        $this->assertIsString($product->sku);
        $this->assertNotSame('', $product->sku);
        $this->assertSame(['kameez', 'dupatta', 'trouser'], $product->included_pieces);
        $this->assertSame([
            'kameez' => 40,
            'dupatta' => 2.5,
            'trouser' => 39,
        ], $product->piece_lengths);
        $this->assertCount(1, $product->media_paths);
        Storage::disk('public')->assertExists($product->media_paths[0]);

        $brand = Brand::where('name', 'Lime')->firstOrFail();
        $this->assertFalse($brand->active);
        Storage::disk('public')->assertExists($brand->logo_path);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'brand_id' => $brand->id,
        ]);

        $slide = HomepageSlide::where('title', 'Lime collection')->firstOrFail();
        $this->assertFalse($slide->active);
        $this->assertSame('/shop', $slide->button_url);
        $this->assertSame('Shop now', $slide->button_text);
        Storage::disk('public')->assertExists($slide->image_path);

        $this->withSession(['admin_user_id' => $user->id])
            ->postJson('/admin/catalog-imports/'.$import->id.'/finalize', $payload)
            ->assertConflict();

        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('homepage_slides', 1);

        $this->withSession(['admin_user_id' => $user->id])
            ->get(route('catalog-imports.review', $import))
            ->assertOk()
            ->assertSee('Lime Vol-552 — Design 01')
            ->assertSee('Publish catalog');

        $this->withSession(['admin_user_id' => $user->id])
            ->post(route('catalog-imports.publish', $import))
            ->assertRedirect(route('catalog-imports.review', $import))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'active' => true,
        ]);
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'active' => true,
        ]);
        $this->assertDatabaseHas('homepage_slides', [
            'id' => $slide->id,
            'active' => true,
        ]);
        $this->assertDatabaseHas('catalog_imports', [
            'id' => $import->id,
            'status' => 'published',
        ]);

        $this->withSession(['admin_user_id' => $user->id])
            ->delete('/admin/catalog-imports/'.$import->id)
            ->assertRedirect();

        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('brands', 0);
        $this->assertDatabaseCount('homepage_slides', 0);
        Storage::disk('public')->assertMissing($product->media_paths[0]);
        Storage::disk('public')->assertMissing($brand->logo_path);
        Storage::disk('public')->assertMissing($slide->image_path);
    }

    public function test_catalog_builder_uses_managed_piece_fabrics_and_two_seasons(): void
    {
        $user = User::factory()->create();
        Fabric::factory()->create(['name' => 'Linen']);

        $this->withSession(['admin_user_id' => $user->id])
            ->get(route('catalog-imports.create'))
            ->assertOk()
            ->assertSee('Kameez fabric')
            ->assertSee('Shalwar / trouser fabric')
            ->assertSee('Dupatta fabric')
            ->assertSee('<option value="Summer">Summer</option>', false)
            ->assertSee('<option value="Winter">Winter</option>', false)
            ->assertSee('Linen');
    }

    public function test_catalog_import_rejects_an_unsupported_season(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $import = CatalogImport::create([
            'name' => 'Unsupported Season Collection',
            'pdf_path' => 'catalog-imports/test.pdf',
            'status' => 'uploaded',
        ]);

        $this->withSession(['admin_user_id' => $user->id])
            ->postJson(route('catalog-imports.finalize', $import), [
                'products' => [[
                    'name' => 'Spring Design',
                    'sku' => 'SPRING-DESIGN-1',
                    'retail_price' => 1000,
                    'stock_quantity' => 1,
                    'gender' => 'women',
                    'season' => 'Spring',
                    'images' => ['data:image/png;base64,'.base64_encode(str_repeat('a', 128))],
                ]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('products.0.season');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_manual_catalog_imports_create_only_inactive_products(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $import = CatalogImport::create([
            'name' => 'Unknown Collection',
            'pdf_path' => 'catalog-imports/test.pdf',
            'status' => 'uploaded',
        ]);
        $image = 'data:image/png;base64,'.base64_encode(str_repeat('a', 128));

        $this->withSession(['admin_user_id' => $user->id])
            ->postJson('/admin/catalog-imports/'.$import->id.'/finalize', [
                'products' => [[
                    'name' => 'Manual Design',
                    'sku' => 'MANUAL-DESIGN-001',
                    'retail_price' => 1000,
                    'stock_quantity' => 1,
                    'gender' => 'women',
                    'images' => [$image],
                ]],
            ])
            ->assertOk();

        $this->assertDatabaseHas('products', [
            'name' => 'Manual Design',
            'catalog_import_id' => $import->id,
            'active' => false,
        ]);
        $this->assertIsString($import->products()->firstOrFail()->sku);
        $this->assertNotSame('', $import->products()->firstOrFail()->sku);
        $this->assertDatabaseCount('brands', 0);
        $this->assertDatabaseCount('homepage_slides', 0);
    }

    public function test_slider_details_require_a_slider_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $import = CatalogImport::create([
            'name' => 'LIME VOL-552',
            'pdf_path' => 'catalog-imports/test.pdf',
            'status' => 'uploaded',
        ]);

        $this->withSession(['admin_user_id' => $user->id])
            ->postJson('/admin/catalog-imports/'.$import->id.'/finalize', [
                'products' => [[
                    'name' => 'Lime Vol-552 — Design 01',
                    'sku' => 'LIME-552-DESIGN-01',
                    'retail_price' => 6200,
                    'stock_quantity' => 3,
                    'gender' => 'women',
                    'images' => ['data:image/png;base64,'.base64_encode(str_repeat('a', 128))],
                ]],
                'slider_title' => 'Missing image',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('slider_image');

        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('brands', 0);
        $this->assertDatabaseCount('homepage_slides', 0);
    }

    public function test_catalog_level_product_details_apply_to_every_design_without_creating_a_slider(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $import = CatalogImport::create([
            'name' => 'Autumn Collection',
            'pdf_path' => 'catalog-imports/test.pdf',
            'status' => 'uploaded',
        ]);
        $image = 'data:image/png;base64,'.base64_encode(str_repeat('a', 128));
        $kameezFabric = Fabric::factory()->create(['name' => 'Cotton lawn']);
        $trouserFabric = Fabric::factory()->create(['name' => 'Cambric']);
        $dupattaFabric = Fabric::factory()->create(['name' => 'Chiffon']);
        $sharedDetails = [
            'description' => 'Matching embroidered outfits.',
            'retail_price' => 5000,
            'discount_percent' => 10,
            'stock_quantity' => 7,
            'gender' => 'women',
            'piece_count' => 3,
            'included_pieces' => ['kameez', 'dupatta', 'trouser'],
            'fabric' => 'Cotton lawn',
            'season' => 'Summer',
            'kameez_fabric_id' => $kameezFabric->id,
            'trouser_fabric_id' => $trouserFabric->id,
            'dupatta_fabric_id' => $dupattaFabric->id,
            'images' => [$image],
        ];

        $this->withSession(['admin_user_id' => $user->id])
            ->postJson('/admin/catalog-imports/'.$import->id.'/finalize', [
                'products' => [
                    ['name' => 'Autumn Collection — Design 01', 'sku' => 'AUTUMN-DESIGN-01', ...$sharedDetails],
                    ['name' => 'Autumn Collection — Design 02', 'sku' => 'AUTUMN-DESIGN-02', ...$sharedDetails],
                ],
            ])
            ->assertOk();

        $this->assertDatabaseHas('products', [
            'name' => 'Autumn Collection — Design 01',
            'description' => 'Matching embroidered outfits.',
            'price' => 4500,
            'compare_at_price' => 5000,
            'discount_percent' => 10,
            'stock_quantity' => 7,
            'piece_count' => 3,
            'fabric' => 'Cotton lawn',
            'season' => 'Summer',
            'kameez_fabric_id' => $kameezFabric->id,
            'trouser_fabric_id' => $trouserFabric->id,
            'dupatta_fabric_id' => $dupattaFabric->id,
            'active' => false,
        ]);
        $catalogProduct = Product::where('name', 'Autumn Collection — Design 01')->firstOrFail();
        $this->assertSame(['kameez', 'dupatta', 'trouser'], $catalogProduct->included_pieces);
        $secondCatalogProduct = Product::where('name', 'Autumn Collection — Design 02')->firstOrFail();
        $this->assertSame(['kameez', 'dupatta', 'trouser'], $secondCatalogProduct->included_pieces);
        $this->assertDatabaseHas('products', [
            'name' => 'Autumn Collection — Design 02',
            'description' => 'Matching embroidered outfits.',
            'price' => 4500,
            'compare_at_price' => 5000,
            'discount_percent' => 10,
            'stock_quantity' => 7,
            'piece_count' => 3,
            'fabric' => 'Cotton lawn',
            'season' => 'Summer',
            'active' => false,
        ]);
        $this->assertCount(2, $import->products()->pluck('sku')->unique());
        $this->assertDatabaseCount('homepage_slides', 0);
    }

    public function test_catalog_import_requires_a_sku_for_every_design(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $import = CatalogImport::create([
            'name' => 'Unknown Collection',
            'pdf_path' => 'catalog-imports/test.pdf',
            'status' => 'uploaded',
        ]);

        $this->withSession(['admin_user_id' => $user->id])
            ->postJson('/admin/catalog-imports/'.$import->id.'/finalize', [
                'products' => [[
                    'name' => 'Design without SKU',
                    'retail_price' => 1000,
                    'stock_quantity' => 1,
                    'gender' => 'women',
                    'images' => ['data:image/png;base64,'.base64_encode(str_repeat('a', 128))],
                ]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('products.0.sku');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_catalog_import_rejects_duplicate_skus(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $import = CatalogImport::create([
            'name' => 'Unknown Collection',
            'pdf_path' => 'catalog-imports/test.pdf',
            'status' => 'uploaded',
        ]);
        $image = 'data:image/png;base64,'.base64_encode(str_repeat('a', 128));
        $sharedDetails = [
            'sku' => 'DUPLICATE-DESIGN-SKU',
            'retail_price' => 1000,
            'stock_quantity' => 1,
            'gender' => 'women',
            'images' => [$image],
        ];

        $this->withSession(['admin_user_id' => $user->id])
            ->postJson('/admin/catalog-imports/'.$import->id.'/finalize', [
                'products' => [
                    ['name' => 'Design 01', ...$sharedDetails],
                    ['name' => 'Design 02', ...$sharedDetails],
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['products.0.sku', 'products.1.sku']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_catalog_import_rejects_an_sku_already_used_by_another_product(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $import = CatalogImport::create([
            'name' => 'Unknown Collection',
            'pdf_path' => 'catalog-imports/test.pdf',
            'status' => 'uploaded',
        ]);
        Product::create([
            'name' => 'Existing Design',
            'slug' => 'existing-design',
            'sku' => 'EXISTING-DESIGN-001',
            'price' => 1000,
            'currency' => 'PKR',
            'stock_quantity' => 1,
            'image_urls' => [],
            'media_paths' => [],
        ]);

        $this->withSession(['admin_user_id' => $user->id])
            ->postJson('/admin/catalog-imports/'.$import->id.'/finalize', [
                'products' => [[
                    'name' => 'Another Design',
                    'sku' => 'EXISTING-DESIGN-001',
                    'retail_price' => 1000,
                    'stock_quantity' => 1,
                    'gender' => 'women',
                    'images' => ['data:image/png;base64,'.base64_encode(str_repeat('a', 128))],
                ]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('products.0.sku');

        $this->assertDatabaseCount('products', 1);
    }

    public function test_unprocessed_catalog_cannot_be_published(): void
    {
        $user = User::factory()->create();
        $import = CatalogImport::create([
            'name' => 'Unknown Collection',
            'pdf_path' => 'catalog-imports/test.pdf',
            'status' => 'uploaded',
        ]);

        $this->withSession(['admin_user_id' => $user->id])
            ->post(route('catalog-imports.publish', $import))
            ->assertRedirect(route('catalog-imports.review', $import))
            ->assertSessionHas('error', 'This catalog must be processed before it can be published.');

        $this->assertDatabaseHas('catalog_imports', [
            'id' => $import->id,
            'status' => 'uploaded',
        ]);
    }
}
