<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBulkSaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_apply_a_discount_to_only_the_selected_products(): void
    {
        $admin = User::factory()->create();
        $saleProduct = $this->createProduct('Sale Product', 1000, 1200, 16.67);
        $unselectedProduct = $this->createProduct('Unselected Product', 700);

        $this->withSession(['admin_user_id' => $admin->id])
            ->from(route('products.index', ['q' => 'Sale', 'page' => 2]))
            ->post(route('products.bulk-sale.update', ['q' => 'Sale', 'page' => 2]), [
                'product_ids' => [$saleProduct->id],
                'action' => 'apply',
                'discount_percent' => 20,
            ])
            ->assertRedirect(route('products.index', ['q' => 'Sale', 'page' => 2]))
            ->assertSessionHas('success', 'Applied a 20% sale to 1 selected product.');

        $this->assertSame('960.00', $saleProduct->refresh()->price);
        $this->assertSame('1200.00', $saleProduct->compare_at_price);
        $this->assertSame('20.00', $saleProduct->discount_percent);
        $this->assertSame('700.00', $unselectedProduct->refresh()->price);
        $this->assertNull($unselectedProduct->compare_at_price);
    }

    public function test_admin_can_remove_sales_and_restore_original_prices(): void
    {
        $admin = User::factory()->create();
        $saleProduct = $this->createProduct('Discounted Product', 800, 1000, 20);
        $regularProduct = $this->createProduct('Regular Product', 1200);

        $this->withSession(['admin_user_id' => $admin->id])
            ->from(route('products.index'))
            ->post(route('products.bulk-sale.update'), [
                'product_ids' => [$saleProduct->id, $regularProduct->id],
                'action' => 'remove',
                'discount_percent' => 'not-a-number',
            ])
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success', 'Removed sales from 2 selected products.');

        $this->assertSame('1000.00', $saleProduct->refresh()->price);
        $this->assertNull($saleProduct->compare_at_price);
        $this->assertSame('0.00', $saleProduct->discount_percent);
        $this->assertSame('1200.00', $regularProduct->refresh()->price);
        $this->assertNull($regularProduct->compare_at_price);
        $this->assertSame('0.00', $regularProduct->discount_percent);
    }

    public function test_admin_sees_bulk_sale_controls_on_the_product_list(): void
    {
        $admin = User::factory()->create();
        $product = $this->createProduct('Listed Product', 1000);

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee('Bulk sale pricing')
            ->assertSee('Apply discount')
            ->assertSee('Remove sale')
            ->assertSee('form="bulk-sale-form"', false)
            ->assertSee('value="'.$product->id.'"', false);
    }

    public function test_admin_cannot_apply_a_discount_above_one_hundred_percent(): void
    {
        $admin = User::factory()->create();
        $product = $this->createProduct('Invalid Discount Product', 1000);

        $this->withSession(['admin_user_id' => $admin->id])
            ->from(route('products.index'))
            ->post(route('products.bulk-sale.update'), [
                'product_ids' => [$product->id],
                'action' => 'apply',
                'discount_percent' => 100.01,
            ])
            ->assertRedirect(route('products.index'))
            ->assertSessionHasErrors('discount_percent');

        $this->assertSame('1000.00', $product->refresh()->price);
        $this->assertNull($product->compare_at_price);
        $this->assertSame('0.00', $product->discount_percent);
    }

    public function test_admin_must_select_at_least_one_product_for_a_bulk_sale(): void
    {
        $admin = User::factory()->create();
        $product = $this->createProduct('No Selection Product', 1000);

        $this->withSession(['admin_user_id' => $admin->id])
            ->from(route('products.index'))
            ->post(route('products.bulk-sale.update'), [
                'action' => 'apply',
                'discount_percent' => 20,
            ])
            ->assertRedirect(route('products.index'))
            ->assertSessionHasErrors('product_ids');

        $this->assertSame('1000.00', $product->refresh()->price);
        $this->assertNull($product->compare_at_price);
    }

    public function test_admin_cannot_select_products_that_do_not_exist(): void
    {
        $admin = User::factory()->create();
        $product = $this->createProduct('Existing Product', 1000);

        $this->withSession(['admin_user_id' => $admin->id])
            ->from(route('products.index'))
            ->post(route('products.bulk-sale.update'), [
                'product_ids' => [$product->id + 1],
                'action' => 'apply',
                'discount_percent' => 20,
            ])
            ->assertRedirect(route('products.index'))
            ->assertSessionHasErrors('product_ids.0');

        $this->assertSame('1000.00', $product->refresh()->price);
        $this->assertNull($product->compare_at_price);
    }

    public function test_unauthenticated_request_cannot_update_bulk_sale_prices(): void
    {
        $product = $this->createProduct('Protected Product', 1000);

        $this->post(route('products.bulk-sale.update'), [
            'product_ids' => [$product->id],
            'action' => 'apply',
            'discount_percent' => 20,
        ])
            ->assertRedirect(route('login'));

        $this->assertSame('1000.00', $product->refresh()->price);
        $this->assertNull($product->compare_at_price);
    }

    private function createProduct(
        string $name,
        float $price,
        ?float $compareAtPrice = null,
        float $discountPercent = 0
    ): Product {
        $slug = strtolower(str_replace(' ', '-', $name));

        return Product::create([
            'name' => $name,
            'slug' => $slug,
            'sku' => strtoupper($slug),
            'price' => $price,
            'currency' => 'PKR',
            'stock_quantity' => 3,
            'compare_at_price' => $compareAtPrice,
            'discount_percent' => $discountPercent,
            'image_urls' => [],
            'media_paths' => [],
        ]);
    }
}
