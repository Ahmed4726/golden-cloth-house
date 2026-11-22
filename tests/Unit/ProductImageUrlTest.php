<?php

namespace Tests\Unit;

use App\Models\HomepageSlide;
use App\Models\Product;
use App\Services\ResponsiveImageCache;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageUrlTest extends TestCase
{
    public function test_uploaded_image_urls_use_the_current_host(): void
    {
        $product = new Product;
        $slide = new HomepageSlide(['image_path' => 'products/slides/hero.jpg']);

        $this->assertSame('/storage/products/catalog/design.jpg', $product->imageUrl('products/catalog/design.jpg'));
        $this->assertSame('/storage/products/slides/hero.jpg', $slide->imageUrl());
        $this->assertSame('https://images.example.com/design.jpg', $product->imageUrl('https://images.example.com/design.jpg'));
    }

    public function test_cached_responsive_variants_use_public_static_urls(): void
    {
        Storage::fake('public');
        $imagePath = UploadedFile::fake()
            ->image('catalog.jpg', 1200, 900)
            ->store('products', 'public');

        $this->assertTrue(ResponsiveImageCache::createVariant($imagePath, 320));

        $product = new Product;
        $cachedVariant = ResponsiveImageCache::variantPath($imagePath, 320);

        $this->assertSame(
            Storage::disk('public')->url($cachedVariant),
            $product->imageUrl($imagePath, 320)
        );
    }
}
