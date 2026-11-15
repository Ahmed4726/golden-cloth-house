<?php

namespace Tests\Feature;

use App\Models\Fabric;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FabricProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_product_with_managed_piece_fabrics_and_season(): void
    {
        $admin = User::factory()->create();
        $kameezFabric = Fabric::factory()->create(['name' => 'Cotton Lawn']);
        $trouserFabric = Fabric::factory()->create(['name' => 'Cambric']);
        $dupattaFabric = Fabric::factory()->create(['name' => 'Chiffon']);

        $this->withSession(['admin_user_id' => $admin->id])
            ->post(route('products.store'), [
                'name' => 'Summer Suit',
                'slug' => 'summer-suit',
                'sku' => 'SUMMER-SUIT-1',
                'price' => 4500,
                'currency' => 'PKR',
                'stock_quantity' => 2,
                'kameez_fabric_id' => $kameezFabric->id,
                'trouser_fabric_id' => $trouserFabric->id,
                'dupatta_fabric_id' => $dupattaFabric->id,
                'season' => 'Summer',
            ])
            ->assertRedirect(route('products.index'));

        $product = Product::firstOrFail();
        $this->assertSame($kameezFabric->id, $product->kameez_fabric_id);
        $this->assertSame($trouserFabric->id, $product->trouser_fabric_id);
        $this->assertSame($dupattaFabric->id, $product->dupatta_fabric_id);
        $this->assertSame('Summer', $product->season);

        $this->get(route('shop.product', $product->slug))
            ->assertOk()
            ->assertSee('Cotton Lawn')
            ->assertSee('Cambric')
            ->assertSee('Chiffon')
            ->assertSee('Summer');
    }

    public function test_admin_cannot_save_a_product_with_an_unsupported_season(): void
    {
        $admin = User::factory()->create();

        $this->withSession(['admin_user_id' => $admin->id])
            ->from(route('products.create'))
            ->post(route('products.store'), [
                'name' => 'Spring Suit',
                'sku' => 'SPRING-SUIT-1',
                'price' => 4500,
                'currency' => 'PKR',
                'stock_quantity' => 2,
                'season' => 'Spring',
            ])
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors('season');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_admin_can_add_fabrics_for_product_dropdowns(): void
    {
        $admin = User::factory()->create();

        $this->withSession(['admin_user_id' => $admin->id])
            ->post(route('fabrics.store'), [
                'name' => 'Linen',
                'sort_order' => 5,
                'active' => 1,
            ])
            ->assertRedirect(route('fabrics.index'));

        $this->assertDatabaseHas('fabrics', [
            'name' => 'Linen',
            'sort_order' => 5,
            'active' => true,
        ]);
    }
}
