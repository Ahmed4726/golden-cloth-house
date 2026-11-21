<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\HomepageSlide;
use App\Models\Product;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StorefrontDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_featured_sale_brand_and_category_discovery_links(): void
    {
        $category = Category::create([
            'name' => 'Test Women Printed Collection',
            'slug' => 'test-women-printed',
            'active' => true,
        ]);
        $brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'active' => true,
        ]);
        $this->createProduct($category, $brand, [
            'featured' => true,
            'compare_at_price' => 5000,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Shop by brand')
            ->assertSee('Featured pieces')
            ->assertSee('On sale')
            ->assertSee('Test Brand')
            ->assertSee(route('shop.brand', $brand->slug), false)
            ->assertSee(route('shop.collection', $category->slug), false);
    }

    public function test_brand_and_category_links_show_their_matching_products(): void
    {
        $category = Category::create([
            'name' => 'Test Women Embroidered Collection',
            'slug' => 'test-women-embroidered',
            'active' => true,
        ]);
        $brand = Brand::create([
            'name' => 'Brand Collection',
            'slug' => 'brand-collection',
            'active' => true,
        ]);
        $this->createProduct($category, $brand);

        $this->get(route('shop.brand', $brand->slug))
            ->assertOk()
            ->assertSee('Brand Collection')
            ->assertSee('Discoverable Product');

        $this->get(route('shop.collection', $category->slug))
            ->assertOk()
            ->assertSee('Test Women Embroidered Collection')
            ->assertSee('Discoverable Product');
    }

    public function test_product_detail_gallery_opens_an_accessible_image_viewer(): void
    {
        $product = $this->createProductWithImage(
            'Zoomable Product',
            'zoomable-product',
            'https://example.com/zoomable-product.jpg'
        );

        $this->get(route('shop.product', $product->slug))
            ->assertOk()
            ->assertSee('data-image-viewer-index="0"', false)
            ->assertSee('id="productImageViewer"', false)
            ->assertSee('aria-label="Zoom image"', false);
    }

    public function test_homepage_uses_responsive_collection_images_and_prioritizes_hero_image(): void
    {
        $category = Category::create([
            'name' => 'Responsive Image Collection',
            'slug' => 'responsive-image-collection',
            'active' => true,
        ]);
        $brand = Brand::create([
            'name' => 'Responsive Image Brand',
            'slug' => 'responsive-image-brand',
            'active' => true,
        ]);
        $product = $this->createProductWithImage(
            'Responsive Homepage Product',
            'responsive-homepage-product',
            'products/responsive-homepage-product.jpg'
        );
        $product->update([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ]);
        HomepageSlide::create([
            'title' => 'Responsive Hero Slide',
            'image_path' => 'slides/responsive-hero.jpg',
            'active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('fetchpriority="high"', false)
            ->assertSee('imagesrcset=', false)
            ->assertSee('width=320', false)
            ->assertSee('width=480', false);
    }

    public function test_shop_featured_collection_image_is_stable_for_a_day_and_rotates_the_next_day(): void
    {
        $this->createProductWithImage(
            'First Daily Feature',
            'first-daily-feature',
            'https://example.com/first-daily-feature.jpg'
        );
        $this->createProductWithImage(
            'Second Daily Feature',
            'second-daily-feature',
            'https://example.com/second-daily-feature.jpg'
        );
        $this->travelTo(Carbon::parse('2026-11-18 12:00:00'));

        $firstDayImage = $this->featuredCollectionImage(
            $this->get(route('shop'))->assertOk()->getContent()
        );
        $sameDayImage = $this->featuredCollectionImage(
            $this->get(route('shop'))->assertOk()->getContent()
        );

        $this->travelTo(Carbon::parse('2026-11-19 12:00:00'));
        $nextDayImage = $this->featuredCollectionImage(
            $this->get(route('shop'))->assertOk()->getContent()
        );

        $this->assertSame($firstDayImage, $sameDayImage);
        $this->assertNotSame($firstDayImage, $nextDayImage);
    }

    private function createProduct(Category $category, Brand $brand, array $attributes = []): Product
    {
        return Product::create([
            'name' => 'Discoverable Product',
            'slug' => 'discoverable-product',
            'sku' => 'DISCOVERABLE-1',
            'price' => 4000,
            'currency' => 'PKR',
            'stock_quantity' => 2,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'active' => true,
            'featured' => false,
            'image_urls' => [],
            'media_paths' => [],
            ...$attributes,
        ]);
    }

    private function createProductWithImage(string $name, string $slug, string $imageUrl): Product
    {
        return Product::create([
            'name' => $name,
            'slug' => $slug,
            'sku' => strtoupper(str_replace('-', '', $slug)),
            'price' => 4000,
            'currency' => 'PKR',
            'stock_quantity' => 2,
            'active' => true,
            'image_urls' => [$imageUrl],
            'media_paths' => [],
        ]);
    }

    private function featuredCollectionImage(string $html): string
    {
        $document = new DOMDocument;
        $previousErrorSetting = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorSetting);
        $this->assertTrue($loaded);
        $images = (new DOMXPath($document))->query('//*[@aria-label="Featured collection"]//img');
        $this->assertNotFalse($images);
        $this->assertSame(1, $images->length);

        $image = $images->item(0);
        $this->assertInstanceOf(\DOMElement::class, $image);

        return $image->getAttribute('src');
    }
}
