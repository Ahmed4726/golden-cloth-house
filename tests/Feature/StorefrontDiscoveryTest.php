<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
