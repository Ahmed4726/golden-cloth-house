<?php

namespace Tests\Feature;

use App\Mail\OrderPlacedMail;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CouponCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_percentage_coupon_is_saved_on_the_order_and_counted_once(): void
    {
        Mail::fake();
        $product = $this->createProduct();
        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'discount_value' => 10,
        ]);
        $this->post(route('cart.add', $product), ['quantity' => 1])->assertRedirect(route('cart.index'));

        $this->from(route('checkout'))
            ->post(route('checkout.coupon.store'), ['coupon_code' => ' save10 '])
            ->assertRedirect(route('checkout'))
            ->assertSessionHas('coupon_code', 'SAVE10');

        $this->post(route('checkout.store'), [
            'customer_name' => 'Test Customer',
            'customer_phone' => '03001234567',
            'customer_email' => 'customer@example.com',
            'shipping_address' => '1 Main Road',
            'shipping_city' => 'Gujrat',
        ])->assertRedirect(route('checkout.success', ['order' => Order::firstOrFail()->order_number]));

        $order = Order::firstOrFail();
        $this->get(route('checkout.success', ['order' => $order->order_number]))
            ->assertOk()
            ->assertSee($order->order_number);
        $this->assertSame('400.00', $order->discount_amount);
        $this->assertSame('3600.00', $order->total);
        $this->assertSame('SAVE10', $order->coupon_code);
        $this->assertSame($coupon->id, $order->coupon_id);
        $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'usage_count' => 1]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 0]);
        Mail::assertQueued(OrderPlacedMail::class);
    }

    public function test_fixed_coupon_discount_cannot_exceed_the_subtotal(): void
    {
        $product = $this->createProduct();
        Coupon::factory()->create([
            'code' => 'BIGSAVE',
            'discount_type' => 'fixed',
            'discount_value' => 5000,
        ]);
        $this->post(route('cart.add', $product), ['quantity' => 1]);
        $this->post(route('checkout.coupon.store'), ['coupon_code' => 'BIGSAVE']);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Test Customer',
            'customer_phone' => '03001234567',
            'shipping_address' => '1 Main Road',
            'shipping_city' => 'Gujrat',
        ])->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame('4000.00', $order->discount_amount);
        $this->assertSame('0.00', $order->total);
    }

    public function test_expired_coupon_is_rejected_without_counting_a_redemption(): void
    {
        $this->travelTo(now());
        $product = $this->createProduct();
        $coupon = Coupon::factory()->create([
            'code' => 'EXPIRED',
            'expires_at' => now()->subMinute(),
        ]);
        $this->post(route('cart.add', $product), ['quantity' => 1]);

        $this->from(route('checkout'))
            ->post(route('checkout.coupon.store'), ['coupon_code' => $coupon->code])
            ->assertRedirect(route('checkout'))
            ->assertSessionHasErrors(['coupon_code' => 'This coupon has expired.']);

        $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'usage_count' => 0]);
    }

    public function test_coupon_minimum_order_amount_is_enforced(): void
    {
        $product = $this->createProduct();
        Coupon::factory()->create([
            'code' => 'MINIMUM',
            'minimum_order_amount' => 5000,
        ]);
        $this->post(route('cart.add', $product), ['quantity' => 1]);

        $this->from(route('checkout'))
            ->post(route('checkout.coupon.store'), ['coupon_code' => 'MINIMUM'])
            ->assertRedirect(route('checkout'))
            ->assertSessionHasErrors(['coupon_code' => 'Your order does not meet the minimum amount for this coupon.']);
    }

    public function test_coupon_with_no_redemptions_remaining_is_rejected(): void
    {
        $product = $this->createProduct();
        Coupon::factory()->create([
            'code' => 'SOLDOUT',
            'max_redemptions' => 1,
            'usage_count' => 1,
        ]);
        $this->post(route('cart.add', $product), ['quantity' => 1]);

        $this->from(route('checkout'))
            ->post(route('checkout.coupon.store'), ['coupon_code' => 'SOLDOUT'])
            ->assertRedirect(route('checkout'))
            ->assertSessionHasErrors(['coupon_code' => 'This coupon has reached its redemption limit.']);
    }

    public function test_inactive_coupon_is_rejected(): void
    {
        $product = $this->createProduct();
        Coupon::factory()->create([
            'code' => 'INACTIVE',
            'active' => false,
        ]);
        $this->post(route('cart.add', $product), ['quantity' => 1]);

        $this->from(route('checkout'))
            ->post(route('checkout.coupon.store'), ['coupon_code' => 'INACTIVE'])
            ->assertRedirect(route('checkout'))
            ->assertSessionHasErrors(['coupon_code' => 'This coupon is not active.']);
    }

    public function test_coupon_cannot_be_used_before_its_start_time(): void
    {
        $this->travelTo(now());
        $product = $this->createProduct();
        Coupon::factory()->create([
            'code' => 'FUTURE',
            'starts_at' => now()->addMinute(),
        ]);
        $this->post(route('cart.add', $product), ['quantity' => 1]);

        $this->from(route('checkout'))
            ->post(route('checkout.coupon.store'), ['coupon_code' => 'FUTURE'])
            ->assertRedirect(route('checkout'))
            ->assertSessionHasErrors(['coupon_code' => 'This coupon is not available yet.']);
    }

    public function test_coupon_per_customer_limit_is_rechecked_when_order_is_placed(): void
    {
        $product = $this->createProduct();
        $customer = Customer::create([
            'identity_key' => 'phone:03001234567',
            'customer_name' => 'Test Customer',
            'customer_phone' => '03001234567',
            'phone_normalized' => '03001234567',
        ]);
        $coupon = Coupon::factory()->create([
            'code' => 'ONCE',
            'per_customer_limit' => 1,
        ]);
        Order::create([
            'order_number' => 'GCH-TEST-ORDER-001',
            'customer_id' => $customer->id,
            'customer_name' => 'Test Customer',
            'customer_phone' => '03001234567',
            'shipping_address' => '1 Main Road',
            'shipping_city' => 'Gujrat',
            'subtotal' => 4000,
            'total' => 4000,
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
        ]);
        $this->post(route('cart.add', $product), ['quantity' => 1]);
        $this->post(route('checkout.coupon.store'), ['coupon_code' => $coupon->code]);

        $this->from(route('checkout'))
            ->post(route('checkout.store'), [
                'customer_name' => 'Test Customer',
                'customer_phone' => '03001234567',
                'shipping_address' => '1 Main Road',
                'shipping_city' => 'Gujrat',
            ])
            ->assertRedirect(route('checkout'))
            ->assertSessionHasErrors('coupon_code');

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'usage_count' => 0]);
    }

    public function test_admin_can_create_a_coupon_with_a_normalized_code(): void
    {
        $admin = User::factory()->create();

        $this->withSession(['admin_user_id' => $admin->id])
            ->post(route('coupons.store'), [
                'code' => ' save20 ',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'active' => 1,
            ])
            ->assertRedirect(route('coupons.index'));

        $this->assertDatabaseHas('coupons', [
            'code' => 'SAVE20',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'active' => true,
        ]);
    }

    private function createProduct(): Product
    {
        config(['store.free_shipping_threshold' => 3000]);

        return Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'TEST-PRODUCT-1',
            'price' => 4000,
            'currency' => 'PKR',
            'stock_quantity' => 1,
            'active' => true,
            'image_urls' => [],
            'media_paths' => [],
        ]);
    }
}
