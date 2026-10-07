<?php

namespace Tests\Feature;

use App\Models\CatalogImport;
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
                'pdf' => UploadedFile::fake()->create('LIME-VOL-552.pdf', 500, 'application/pdf'),
            ])
            ->assertRedirect();

        $import = CatalogImport::firstOrFail();
        Storage::disk('public')->assertExists($import->pdf_path);
    }

    public function test_admin_can_create_a_product_from_selected_pdf_images_with_three_percent_discount(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $import = CatalogImport::create([
            'name' => 'LIME VOL-552',
            'pdf_path' => 'catalog-imports/test.pdf',
            'status' => 'uploaded',
        ]);

        $png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

        $this->withSession(['admin_user_id' => $user->id])
            ->postJson('/admin/catalog-imports/'.$import->id.'/finalize', [
                'products' => [[
                    'name' => 'Lime Vol-552 — Design 01',
                    'description' => 'Peach Slub Embroidered with Peach Slub Embroidered Dupatta.',
                    'retail_price' => 6200,
                    'discount_percent' => 3,
                    'stock_quantity' => 3,
                    'gender' => 'women',
                    'piece_count' => 3,
                    'fabric' => 'Peach Slub',
                    'season' => 'Winter',
                    'design_code' => 'DES-01',
                    'images' => [$png],
                ]],
            ])
            ->assertOk()
            ->assertJsonPath('message', '1 products created successfully.');

        $this->assertDatabaseHas('products', [
            'name' => 'Lime Vol-552 — Design 01',
            'price' => 6014,
            'compare_at_price' => 6200,
            'discount_percent' => 3,
            'gender' => 'women',
            'wear_type' => 'unstitched',
            'catalog_import_id' => $import->id,
        ]);

        $product = $import->products()->firstOrFail();
        $this->assertCount(1, $product->media_paths);
        Storage::disk('public')->assertExists($product->media_paths[0]);
    }
}
