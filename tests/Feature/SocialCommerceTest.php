<?php

namespace Tests\Feature;

use App\Enums\SocialProvider;
use App\Jobs\PublishSocialPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SocialConnection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SocialCommerceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_shop_is_accessible(): void
    {
        $this->get('/')->assertOk()->assertSee('Golden Cloth House');
    }

    public function test_admin_dashboard_is_protected(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_can_open_dashboard(): void
    {
        $user = User::factory()->create();

        $this->withSession(['admin_user_id' => $user->id])
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_admin_can_manage_categories_and_brands(): void
    {
        $user = User::factory()->create();
        $session = ['admin_user_id' => $user->id];

        $this->withSession($session)->post('/admin/categories', [
            'name' => 'Winter Collection',
            'description' => 'Warm clothing',
            'active' => 1,
        ])->assertRedirect('/admin/categories');

        $this->withSession($session)->post('/admin/brands', [
            'name' => 'Golden Label',
            'description' => 'House brand',
            'active' => 1,
        ])->assertRedirect('/admin/brands');

        $this->assertDatabaseHas('categories', ['name' => 'Winter Collection', 'slug' => 'winter-collection']);
        $this->assertDatabaseHas('brands', ['name' => 'Golden Label', 'slug' => 'golden-label']);
    }

    public function test_product_can_be_published_to_selected_social_accounts_using_product_content(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $category = Category::create(['name' => 'Shirts', 'slug' => 'shirts', 'active' => true]);
        $brand = Brand::create(['name' => 'Golden Label', 'slug' => 'golden-label', 'active' => true]);
        $product = Product::create([
            'name' => 'Premium Shirt',
            'slug' => 'premium-shirt',
            'price' => 3500,
            'currency' => 'PKR',
            'stock_quantity' => 10,
            'category' => 'Shirts',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'description' => 'Premium cotton shirt.',
            'image_urls' => ['https://example.com/shirt.jpg'],
            'media_paths' => [],
            'active' => true,
        ]);
        $connection = SocialConnection::create([
            'provider' => SocialProvider::Facebook,
            'name' => 'Test Page',
            'external_id' => '123',
            'access_token' => 'token',
            'active' => true,
            'status' => 'connected',
        ]);

        $this->withSession(['admin_user_id' => $user->id])
            ->post('/admin/posts', [
                'product_id' => $product->id,
                'media_type' => 'image',
                'connections' => [$connection->id],
                'action' => 'publish',
            ])
            ->assertRedirect();

        Bus::assertDispatched(PublishSocialPost::class);
        $this->assertDatabaseHas('social_posts', [
            'product_id' => $product->id,
            'title' => 'Premium Shirt',
            'caption' => 'Premium cotton shirt.',
            'status' => 'queued',
        ]);
    }

    public function test_readiness_endpoint_has_all_providers(): void
    {
        $user = User::factory()->create();

        $this->withSession(['admin_user_id' => $user->id])
            ->get('/admin/api/readiness')
            ->assertOk()
            ->assertJsonStructure(['facebook', 'instagram', 'tiktok', 'youtube']);
    }
}
