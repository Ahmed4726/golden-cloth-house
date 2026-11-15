<?php

namespace Tests\Feature;

use App\Enums\SocialProvider;
use App\Models\Brand;
use App\Models\CatalogImport;
use App\Models\Category;
use App\Models\HomepageSlide;
use App\Models\Product;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminListFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_and_filter_products(): void
    {
        $admin = User::factory()->create();
        $category = Category::create(['name' => 'Women Unstitched', 'slug' => 'women-unstitched']);
        $brand = Brand::create(['name' => 'Zeest', 'slug' => 'zeest']);

        Product::create([
            'name' => 'Zeest Vol-259 Design 01',
            'slug' => 'zeest-vol-259-design-01',
            'sku' => 'ZEEST-259-01',
            'price' => 4999,
            'currency' => 'PKR',
            'stock_quantity' => 3,
            'category' => 'Women Unstitched',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'active' => true,
            'image_urls' => [],
            'media_paths' => [],
        ]);
        Product::create([
            'name' => 'Zeest Hidden Design',
            'slug' => 'zeest-hidden-design',
            'sku' => 'ZEEST-HIDDEN',
            'price' => 4999,
            'currency' => 'PKR',
            'stock_quantity' => 3,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'active' => false,
            'image_urls' => [],
            'media_paths' => [],
        ]);

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('products.index', [
                'q' => 'ZEEST-259-01',
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'status' => 'active',
            ]))
            ->assertOk()
            ->assertSee('Zeest Vol-259 Design 01')
            ->assertDontSee('Zeest Hidden Design')
            ->assertSee('value="ZEEST-259-01"', false);
    }

    public function test_admin_can_search_and_filter_categories_and_brands(): void
    {
        $admin = User::factory()->create();
        Category::create(['name' => 'Women Unstitched', 'slug' => 'women-unstitched', 'active' => true]);
        Category::create(['name' => 'Women Hidden', 'slug' => 'women-hidden', 'active' => false]);
        Brand::create(['name' => 'Zeest', 'slug' => 'zeest', 'active' => true]);
        Brand::create(['name' => 'Zeest Archive', 'slug' => 'zeest-archive', 'active' => false]);

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('categories.index', ['q' => 'Women', 'status' => 'active']))
            ->assertOk()
            ->assertSee('Women Unstitched')
            ->assertDontSee('Women Hidden');

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('brands.index', ['q' => 'Zeest', 'status' => 'active']))
            ->assertOk()
            ->assertSee('Zeest')
            ->assertDontSee('Zeest Archive');
    }

    public function test_admin_can_filter_and_paginate_slides(): void
    {
        $admin = User::factory()->create();

        foreach (range(1, 21) as $number) {
            HomepageSlide::create([
                'title' => 'Campaign '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'active' => true,
            ]);
        }
        HomepageSlide::create(['title' => 'Campaign Hidden', 'active' => false]);

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('slides.index', ['q' => 'Campaign', 'status' => 'active', 'page' => 2]))
            ->assertOk()
            ->assertSee('Campaign 21')
            ->assertDontSee('Campaign Hidden')
            ->assertSee('page=1', false);
    }

    public function test_admin_can_filter_catalogs_and_social_posts(): void
    {
        $admin = User::factory()->create();
        CatalogImport::create([
            'name' => 'Zeest Vol-259',
            'pdf_path' => 'catalog-imports/zeest.pdf',
            'status' => 'published',
        ]);
        CatalogImport::create([
            'name' => 'Zeest Draft Catalog',
            'pdf_path' => 'catalog-imports/draft.pdf',
            'status' => 'uploaded',
        ]);
        SocialPost::create(['title' => 'Zeest Launch', 'caption' => 'New collection', 'status' => 'published']);
        SocialPost::create(['title' => 'Zeest Draft', 'caption' => 'Coming soon', 'status' => 'draft']);

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('catalog-imports.index', ['q' => 'Zeest', 'status' => 'published']))
            ->assertOk()
            ->assertSee('Zeest Vol-259')
            ->assertDontSee('Zeest Draft Catalog');

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('posts.index', ['q' => 'New collection', 'status' => 'published']))
            ->assertOk()
            ->assertSee('Zeest Launch')
            ->assertDontSee('Zeest Draft');
    }

    public function test_admin_can_filter_social_connections_by_provider_and_enabled_state(): void
    {
        $admin = User::factory()->create();
        SocialConnection::create([
            'provider' => SocialProvider::Facebook,
            'name' => 'Golden Facebook Page',
            'active' => true,
            'status' => 'connected',
        ]);
        SocialConnection::create([
            'provider' => SocialProvider::Instagram,
            'name' => 'Golden Instagram',
            'active' => false,
            'status' => 'pending',
        ]);

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('connections.index', [
                'q' => 'Golden Facebook',
                'provider' => 'facebook',
                'active' => '1',
            ]))
            ->assertOk()
            ->assertSee('Golden Facebook Page')
            ->assertDontSee('Golden Instagram');
    }
}
