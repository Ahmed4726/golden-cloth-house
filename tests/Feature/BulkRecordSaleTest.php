<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkRecordSaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_record_multiple_product_sales_and_storefront_shows_sold_out(): void
    {
        $admin = User::factory()->create();
        $firstProduct = $this->createProduct('First Bulk Product', 2500, 2);
        $secondProduct = $this->createProduct('Second Bulk Product', 1700, 1);

        $response = $this->withSession(['admin_user_id' => $admin->id])
            ->from(route('products.index'))
            ->post(route('orders.bulk-sales.store'), [
                'product_ids' => [$firstProduct->id, $secondProduct->id],
                'quantities' => [
                    $firstProduct->id => 2,
                    $secondProduct->id => 1,
                ],
            ]);

        $order = Order::firstOrFail();
        $response->assertRedirect(route('orders.show', $order))
            ->assertSessionHas('success', 'Bulk in-store sale recorded and stock updated.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'delivered',
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'currency' => 'PKR',
            'subtotal' => 6700,
            'total' => 6700,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $firstProduct->id,
            'quantity' => 2,
            'unit_price' => 2500,
            'line_total' => 5000,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $secondProduct->id,
            'quantity' => 1,
            'unit_price' => 1700,
            'line_total' => 1700,
        ]);
        $this->assertSame(0, $firstProduct->fresh()->stock_quantity);
        $this->assertSame(0, $secondProduct->fresh()->stock_quantity);

        $this->get(route('shop.product', $firstProduct->slug))
            ->assertOk()
            ->assertSee('Sold out');
        $this->get(route('shop'))
            ->assertOk()
            ->assertSee('Sold out');
    }

    public function test_bulk_sale_with_insufficient_stock_does_not_create_an_order_or_partially_deduct_stock(): void
    {
        $admin = User::factory()->create();
        $availableProduct = $this->createProduct('Available Bulk Product', 1200, 3);
        $lowStockProduct = $this->createProduct('Low Stock Product', 900, 1);

        $this->withSession(['admin_user_id' => $admin->id])
            ->from(route('products.index'))
            ->post(route('orders.bulk-sales.store'), [
                'product_ids' => [$availableProduct->id, $lowStockProduct->id],
                'quantities' => [
                    $availableProduct->id => 1,
                    $lowStockProduct->id => 2,
                ],
            ])
            ->assertRedirect(route('products.index'))
            ->assertSessionHasErrors([
                'quantities.'.$lowStockProduct->id => 'Only 1 item(s) are currently in stock.',
            ]);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(3, $availableProduct->fresh()->stock_quantity);
        $this->assertSame(1, $lowStockProduct->fresh()->stock_quantity);
    }

    public function test_bulk_sale_requires_a_quantity_for_each_selected_product(): void
    {
        $admin = User::factory()->create();
        $product = $this->createProduct('Missing Quantity Product', 1300, 2);

        $this->withSession(['admin_user_id' => $admin->id])
            ->from(route('products.index'))
            ->post(route('orders.bulk-sales.store'), [
                'product_ids' => [$product->id],
                'quantities' => [$product->id + 1 => 1],
            ])
            ->assertRedirect(route('products.index'))
            ->assertSessionHasErrors([
                'quantities.'.$product->id => 'Enter a quantity for each selected product.',
            ]);

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(2, $product->fresh()->stock_quantity);
    }

    public function test_bulk_sale_rejects_products_with_different_currencies(): void
    {
        $admin = User::factory()->create();
        $pkrProduct = $this->createProduct('PKR Bulk Product', 1300, 2);
        $usdProduct = $this->createProduct('USD Bulk Product', 20, 2, 'USD');

        $this->withSession(['admin_user_id' => $admin->id])
            ->from(route('products.index'))
            ->post(route('orders.bulk-sales.store'), [
                'product_ids' => [$pkrProduct->id, $usdProduct->id],
                'quantities' => [
                    $pkrProduct->id => 1,
                    $usdProduct->id => 1,
                ],
            ])
            ->assertRedirect(route('products.index'))
            ->assertSessionHasErrors([
                'product_ids' => 'Selected products must use the same currency.',
            ]);

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(2, $pkrProduct->fresh()->stock_quantity);
        $this->assertSame(2, $usdProduct->fresh()->stock_quantity);
    }

    public function test_unauthenticated_request_cannot_record_a_bulk_sale(): void
    {
        $product = $this->createProduct('Protected Bulk Product', 1000, 2);

        $this->post(route('orders.bulk-sales.store'), [
            'product_ids' => [$product->id],
            'quantities' => [$product->id => 1],
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(2, $product->fresh()->stock_quantity);
    }

    private function createProduct(string $name, int $price, int $stock, string $currency = 'PKR'): Product
    {
        $slug = strtolower(str_replace(' ', '-', $name));

        return Product::create([
            'name' => $name,
            'slug' => $slug,
            'sku' => strtoupper($slug),
            'price' => $price,
            'currency' => $currency,
            'stock_quantity' => $stock,
            'active' => true,
            'image_urls' => [],
            'media_paths' => [],
        ]);
    }
}
