<?php

namespace Tests\Feature;

use App\Enums\SocialProvider;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
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
        Http::preventStrayRequests();
        Http::fake([
            'https://graph.facebook.com/123/photos' => Http::response(['post_id' => 'facebook-post-456']),
        ]);

        $user = User::factory()->create();
        $category = Category::create(['name' => 'Shirts', 'slug' => 'shirts', 'active' => true]);
        $brand = Brand::create(['name' => 'Golden Label', 'slug' => 'golden-label', 'active' => true]);
        $product = Product::create([
            'name' => 'Premium Shirt',
            'slug' => 'premium-shirt',
            'sku' => 'PREMIUM-SHIRT-001',
            'price' => 3500,
            'currency' => 'PKR',
            'stock_quantity' => 10,
            'category' => 'Shirts',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'description' => 'Premium cotton shirt.',
            'image_urls' => ['/storage/products/shirt.jpg'],
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

        $response = $this->withSession(['admin_user_id' => $user->id])
            ->post('/admin/posts', [
                'product_id' => $product->id,
                'media_type' => 'image',
                'connections' => [$connection->id],
                'action' => 'publish',
            ])
            ->assertRedirect();

        $response->assertSessionHas('success', 'Post published successfully.');
        $this->assertDatabaseHas('social_posts', [
            'product_id' => $product->id,
            'title' => 'Premium Shirt',
            'caption' => 'Premium cotton shirt.',
            'status' => 'published',
        ]);
        $this->assertDatabaseHas('social_post_publications', [
            'social_connection_id' => $connection->id,
            'status' => 'published',
            'external_id' => 'facebook-post-456',
        ]);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://graph.facebook.com/123/photos'
            && $request['caption'] === 'Premium cotton shirt.'
            && $request['url'] === rtrim((string) config('app.url'), '/').'/storage/products/shirt.jpg'
            && $request['access_token'] === 'token');
    }

    public function test_failed_social_publication_is_saved_and_reported_to_the_admin(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://graph.facebook.com/123/photos' => Http::response(['error' => 'Invalid access token'], 401),
        ]);

        $user = User::factory()->create();
        $connection = SocialConnection::create([
            'provider' => SocialProvider::Facebook,
            'name' => 'Test Page',
            'external_id' => '123',
            'access_token' => 'expired-token',
            'active' => true,
            'status' => 'connected',
        ]);

        $response = $this->withSession(['admin_user_id' => $user->id])
            ->post('/admin/posts', [
                'media_type' => 'image',
                'media_urls' => 'https://example.com/image.jpg',
                'connections' => [$connection->id],
                'action' => 'publish',
            ])
            ->assertRedirect();

        $response->assertSessionHas('error', 'Social publishing failed: Facebook publish failed: {"error":"Invalid access token"}');
        $post = SocialPost::firstOrFail();
        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee('Social publishing failed: Facebook publish failed: {"error":"Invalid access token"}');
        $this->assertDatabaseHas('social_posts', ['status' => 'failed']);
        $this->assertDatabaseHas('social_post_publications', [
            'social_connection_id' => $connection->id,
            'status' => 'failed',
            'error' => 'Facebook publish failed: {"error":"Invalid access token"}',
        ]);
        $this->assertDatabaseHas('social_connections', [
            'id' => $connection->id,
            'status' => 'error',
            'last_error' => 'Facebook publish failed: {"error":"Invalid access token"}',
        ]);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://graph.facebook.com/123/photos');
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
