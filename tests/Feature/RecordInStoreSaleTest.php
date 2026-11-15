<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordInStoreSaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_record_an_in_store_sale_without_customer_details(): void
    {
        $admin = User::factory()->create();
        $product = Product::create([
            'name' => 'Classic Kurta',
            'slug' => 'classic-kurta',
            'sku' => 'CLASSIC-KURTA-001',
            'price' => 3500,
            'currency' => 'PKR',
            'stock_quantity' => 5,
            'image_urls' => [],
            'media_paths' => [],
        ]);

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('products.sales.create', $product))
            ->assertOk()
            ->assertSee('Customer details')
            ->assertSee('Available stock: 5');

        $response = $this->withSession(['admin_user_id' => $admin->id])
            ->post(route('products.sales.store', $product), ['quantity' => 2]);

        $order = Order::firstOrFail();
        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame(3, $product->fresh()->stock_quantity);
        $this->assertSame('cash', $order->payment_method);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('delivered', $order->status);
        $this->assertNull($order->customer_name);
        $this->assertNull($order->customer_phone);
        $this->assertNull($order->shipping_address);
        $this->assertNull($order->shipping_city);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 3500,
            'line_total' => 7000,
        ]);
    }

    public function test_admin_can_save_optional_customer_contact_and_address_details(): void
    {
        $admin = User::factory()->create();
        $product = Product::create([
            'name' => 'Printed Suit',
            'slug' => 'printed-suit',
            'sku' => 'PRINTED-SUIT-001',
            'price' => 4200,
            'currency' => 'PKR',
            'stock_quantity' => 2,
            'image_urls' => [],
            'media_paths' => [],
        ]);

        $this->withSession(['admin_user_id' => $admin->id])
            ->post(route('products.sales.store', $product), [
                'quantity' => 1,
                'customer_name' => 'Ayesha Khan',
                'customer_phone' => '03001234567',
                'customer_email' => 'ayesha@example.com',
                'shipping_address' => 'House 10, Main Road',
                'shipping_city' => 'Gujrat',
                'shipping_area' => 'City Area',
                'customer_note' => 'Repeat customer',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Ayesha Khan',
            'customer_phone' => '03001234567',
            'customer_email' => 'ayesha@example.com',
            'shipping_address' => 'House 10, Main Road',
            'shipping_city' => 'Gujrat',
            'shipping_area' => 'City Area',
            'customer_note' => 'Repeat customer',
        ]);
        $this->assertSame(1, $product->fresh()->stock_quantity);
    }

    public function test_admin_cannot_record_a_sale_above_available_stock(): void
    {
        $admin = User::factory()->create();
        $product = Product::create([
            'name' => 'Limited Fabric',
            'slug' => 'limited-fabric',
            'sku' => 'LIMITED-FABRIC-001',
            'price' => 2800,
            'currency' => 'PKR',
            'stock_quantity' => 1,
            'image_urls' => [],
            'media_paths' => [],
        ]);

        $this->withSession(['admin_user_id' => $admin->id])
            ->from(route('products.sales.create', $product))
            ->post(route('products.sales.store', $product), ['quantity' => 2])
            ->assertRedirect(route('products.sales.create', $product))
            ->assertSessionHasErrors([
                'quantity' => 'Only 1 item(s) are currently in stock.',
            ]);

        $this->assertSame(1, $product->fresh()->stock_quantity);
        $this->assertDatabaseCount('orders', 0);
    }
}
