<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_history_combines_storefront_and_in_store_orders_by_normalized_phone(): void
    {
        $admin = User::factory()->create();
        $storeProduct = Product::create([
            'name' => 'Store Purchase',
            'slug' => 'store-purchase',
            'sku' => 'STORE-001',
            'price' => 3200,
            'currency' => 'PKR',
            'stock_quantity' => 2,
            'image_urls' => [],
            'media_paths' => [],
        ]);
        $onlineProduct = Product::create([
            'name' => 'Online Purchase',
            'slug' => 'online-purchase',
            'sku' => 'ONLINE-001',
            'price' => 4100,
            'currency' => 'PKR',
            'stock_quantity' => 2,
            'active' => true,
            'image_urls' => [],
            'media_paths' => [],
        ]);

        $this->withSession(['admin_user_id' => $admin->id])
            ->post(route('products.sales.store', $storeProduct), [
                'quantity' => 1,
                'customer_name' => 'Ayesha Khan',
                'customer_phone' => '0300 123-4567',
                'shipping_address' => 'House 10, Main Road',
                'shipping_city' => 'Gujrat',
            ])
            ->assertRedirect();

        $this->withSession([
            'cart' => [
                (string) $onlineProduct->id => ['product_id' => $onlineProduct->id, 'quantity' => 1],
            ],
        ])->post(route('checkout.store'), [
            'customer_name' => 'Ayesha Khan',
            'customer_phone' => '03001234567',
            'shipping_address' => 'House 11, Main Road',
            'shipping_city' => 'Gujrat',
        ])->assertRedirect();

        $customer = Customer::sole();

        $this->assertSame('phone:03001234567', $customer->identity_key);
        $this->assertSame(2, $customer->orders()->count());
        $this->assertDatabaseCount('customers', 1);

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('customers.show', $customer))
            ->assertOk()
            ->assertSee('Store Purchase')
            ->assertSee('Online Purchase')
            ->assertSee('House 11, Main Road');

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('customers.index', ['q' => 'Ayesha']))
            ->assertOk()
            ->assertSee('Ayesha Khan')
            ->assertSee('House 11, Main Road');
    }

    public function test_email_identifies_customers_without_a_phone_and_walk_in_sales_are_not_listed(): void
    {
        $admin = User::factory()->create();
        $product = Product::create([
            'name' => 'In-store Purchase',
            'slug' => 'in-store-purchase',
            'sku' => 'INSTORE-001',
            'price' => 2500,
            'currency' => 'PKR',
            'stock_quantity' => 3,
            'image_urls' => [],
            'media_paths' => [],
        ]);

        $this->withSession(['admin_user_id' => $admin->id])
            ->post(route('products.sales.store', $product), [
                'quantity' => 1,
                'customer_name' => 'Customer One',
                'customer_email' => 'shared@example.com',
            ])
            ->assertRedirect();

        $this->withSession(['admin_user_id' => $admin->id])
            ->post(route('products.sales.store', $product), [
                'quantity' => 1,
                'customer_name' => 'Customer One Updated',
                'customer_email' => 'SHARED@example.com',
            ])
            ->assertRedirect();

        $this->withSession(['admin_user_id' => $admin->id])
            ->post(route('products.sales.store', $product), ['quantity' => 1])
            ->assertRedirect();

        $customer = Customer::sole();

        $this->assertSame('email:shared@example.com', $customer->identity_key);
        $this->assertSame(2, $customer->orders()->count());
        $this->assertDatabaseHas('orders', [
            'customer_name' => null,
            'customer_id' => null,
        ]);

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('customers.index'))
            ->assertOk()
            ->assertSee('Customer One Updated')
            ->assertDontSee('Walk-in customer');
    }
}
