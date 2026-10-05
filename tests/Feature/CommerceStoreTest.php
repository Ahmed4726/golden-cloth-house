<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommerceStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created_with_an_uploaded_image(): void
    {
        Storage::fake('public');

        $admin = \App\Models\User::factory()->create();
        $response = $this->withSession(['admin_user_id' => $admin->id])
            ->post('/admin/products', [
                'name' => 'Classic Kurta',
                'price' => 3500,
                'currency' => 'PKR',
                'stock_quantity' => 5,
                'category' => 'Men',
                'description' => 'Premium cotton kurta.',
                'images' => [UploadedFile::fake()->image('kurta.jpg', 1200, 1600)],
                'active' => 1,
            ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::firstOrFail();
        $this->assertCount(1, $product->media_paths);
        Storage::disk('public')->assertExists($product->media_paths[0]);
    }

    public function test_customer_can_add_product_to_cart_and_checkout_with_cod(): void
    {
        $product = Product::create([
            'name' => 'Premium Shalwar Kameez',
            'slug' => 'premium-shalwar-kameez',
            'price' => 4000,
            'currency' => 'PKR',
            'stock_quantity' => 3,
            'active' => true,
            'image_urls' => [],
            'media_paths' => [],
        ]);

        $this->post(route('cart.add', $product), ['quantity' => 2])
            ->assertRedirect(route('cart.index'));

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Muhammad Ahmed',
            'customer_phone' => '03001234567',
            'customer_email' => 'customer@example.com',
            'shipping_city' => 'Gujrat',
            'shipping_area' => 'City Area',
            'shipping_address' => 'House 1, Main Road',
        ]);

        $order = Order::firstOrFail();

        $response->assertRedirect(route('checkout.success', $order));
        $this->assertSame('pending', $order->status);
        $this->assertSame('cod', $order->payment_method);
        $this->assertSame(1, Product::find($product->id)->stock_quantity);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_empty_cart_cannot_checkout(): void
    {
        $this->get(route('checkout'))
            ->assertRedirect(route('shop'))
            ->assertSessionHas('error');
    }
}
