<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
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

        $admin = User::factory()->create();
        $response = $this->withSession(['admin_user_id' => $admin->id])
            ->post('/admin/products', [
                'name' => 'Classic Kurta',
                'price' => 3500,
                'currency' => 'PKR',
                'stock_quantity' => 5,
                'category' => 'Men',
                'description' => 'Premium cotton kurta.',
                'included_pieces' => ['kameez', 'dupatta'],
                'piece_lengths' => [
                    'kameez' => 40,
                    'dupatta' => 2.5,
                    'trouser' => 39,
                ],
                'images' => [UploadedFile::fake()->image('kurta.jpg', 1200, 1600)],
                'active' => 1,
            ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::firstOrFail();
        $this->assertSame(2, $product->piece_count);
        $this->assertSame(['kameez', 'dupatta'], $product->included_pieces);
        $this->assertSame([
            'kameez' => 40,
            'dupatta' => 2.5,
        ], $product->piece_lengths);
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
        $this->assertSame('0.00', $order->shipping_amount);
        $this->assertSame(1, Product::find($product->id)->stock_quantity);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_checkout_applies_flat_shipping_fee_to_orders_below_free_shipping_threshold(): void
    {
        config([
            'store.shipping_fee' => 200,
            'store.free_shipping_threshold' => 5000,
        ]);
        $product = Product::create([
            'name' => 'Everyday Fabric',
            'slug' => 'everyday-fabric',
            'price' => 4000,
            'currency' => 'PKR',
            'stock_quantity' => 2,
            'active' => true,
            'image_urls' => [],
            'media_paths' => [],
        ]);

        $this->withSession([
            'cart' => [
                (string) $product->id => [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ])->post(route('checkout.store'), [
            'customer_name' => 'Muhammad Ahmed',
            'customer_phone' => '03001234567',
            'customer_email' => 'customer@example.com',
            'shipping_city' => 'Gujrat',
            'shipping_area' => 'City Area',
            'shipping_address' => 'House 1, Main Road',
        ])->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame('200.00', $order->shipping_amount);
        $this->assertSame('4200.00', $order->total);
    }

    public function test_empty_cart_cannot_checkout(): void
    {
        $this->get(route('checkout'))
            ->assertRedirect(route('shop'))
            ->assertSessionHas('error');
    }

    public function test_admin_can_update_the_whatsapp_number(): void
    {
        $admin = User::factory()->create();

        $this->withSession(['admin_user_id' => $admin->id])
            ->patch(route('settings.update'), [
                'whatsapp_number' => '+92 (300) 123-4567',
            ])
            ->assertRedirect(route('settings.edit'))
            ->assertSessionHas('success', 'WhatsApp number updated successfully.');

        $this->assertDatabaseHas('store_settings', [
            'whatsapp_number' => '923001234567',
        ]);
    }

    public function test_admin_can_view_the_whatsapp_settings_page(): void
    {
        $admin = User::factory()->create();

        $this->withSession(['admin_user_id' => $admin->id])
            ->get(route('settings.edit'))
            ->assertOk()
            ->assertSee('WhatsApp number')
            ->assertSee(route('settings.update'), false);
    }

    public function test_whatsapp_settings_are_only_available_to_admins(): void
    {
        $this->get(route('settings.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_about_page_shows_store_history_address_and_search_details(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('30+ Years in Gujrat')
            ->assertSee('For over 30 years')
            ->assertSee('Women’s unstitched suits')
            ->assertSee('Amin Market, Madina Bazar, Sargodha Road, Gujrat, Pakistan')
            ->assertSee('"@type":"AboutPage"', false)
            ->assertSee('Our story')
            ->assertSee('Contact');
    }

    public function test_contact_page_shows_gujrat_address_and_map_directions(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Amin Market, Madina Bazar, Sargodha Road, Gujrat, Pakistan')
            ->assertSee('Looking for a fabric shop in Gujrat?')
            ->assertSee('Get directions')
            ->assertSee('https://www.google.com/maps/search/?api=1&amp;query=Amin%20Market%2C%20Madina%20Bazar%2C%20Sargodha%20Road%2C%20Gujrat%2C%20Pakistan', false)
            ->assertSee('Track your order')
            ->assertSee('Read frequently asked questions');
    }

    public function test_admin_cannot_save_an_invalid_whatsapp_number(): void
    {
        $admin = User::factory()->create();

        $this->withSession(['admin_user_id' => $admin->id])
            ->patch(route('settings.update'), [
                'whatsapp_number' => 'not-a-phone-number',
            ])
            ->assertSessionHasErrors('whatsapp_number');

        $this->assertDatabaseCount('store_settings', 0);
    }

    public function test_product_page_has_a_prefilled_whatsapp_order_link(): void
    {
        $product = Product::create([
            'name' => 'Premium Shalwar Kameez',
            'slug' => 'premium-shalwar-kameez',
            'price' => 4000,
            'currency' => 'PKR',
            'stock_quantity' => 3,
            'active' => true,
            'image_urls' => [
                'https://example.com/product-front.jpg',
                'https://example.com/product-back.jpg',
            ],
            'media_paths' => [],
        ]);
        StoreSetting::create(['whatsapp_number' => '923001234567']);

        $message = "Hello, I would like to order this product:\n"
            ."Premium Shalwar Kameez\nQuantity: 1\nUnit price: 4,000 PKR\nProduct link: "
            .route('shop.product', $product);
        $expectedLink = 'href="https://wa.me/923001234567?text='.rawurlencode($message).'"';

        $this->get(route('shop.product', $product))
            ->assertOk()
            ->assertSee('ratio-7x5', false)
            ->assertSee('productGallery', false)
            ->assertSee('data-bs-slide-to="1"', false)
            ->assertSee('Free delivery on orders of 5,000 PKR or more.')
            ->assertSee('flat 200 PKR delivery fee')
            ->assertSee('Premium Shalwar Kameez')
            ->assertSee('Order via WhatsApp')
            ->assertSee($expectedLink, false);
    }

    public function test_quick_view_includes_suit_details_and_a_whatsapp_order_button(): void
    {
        $product = Product::create([
            'name' => 'Two Piece Suit',
            'slug' => 'two-piece-suit',
            'price' => 4000,
            'currency' => 'PKR',
            'stock_quantity' => 3,
            'piece_count' => 2,
            'included_pieces' => ['kameez', 'dupatta'],
            'piece_lengths' => [
                'kameez' => 40,
                'dupatta' => 2.5,
            ],
            'active' => true,
            'image_urls' => [
                'https://example.com/suit-front.jpg',
                'https://example.com/suit-back.jpg',
            ],
            'media_paths' => [],
        ]);
        StoreSetting::create(['whatsapp_number' => '923001234567']);

        $this->get(route('shop'))
            ->assertOk()
            ->assertSee('ratio-7x5', false)
            ->assertSee('Order via WhatsApp')
            ->assertSee('id="quickViewImage"', false)
            ->assertSee('quick-view-gallery ratio ratio-7x5 bg-light', false)
            ->assertSee('quickViewGalleryIndicators', false)
            ->assertSee('data-product-images=', false)
            ->assertSee('data-product-included-pieces="kameez,dupatta"', false)
            ->assertSee('data-product-piece-count="2"', false)
            ->assertSee('data-product-piece-length-kameez="40"', false)
            ->assertSee('data-product-piece-length-dupatta="2.5"', false)
            ->assertSee('id="quickViewWhatsAppLink"', false)
            ->assertSee('id="quickViewPieceList"', false);
    }

    public function test_product_card_has_responsive_cart_and_whatsapp_order_buttons(): void
    {
        $product = Product::create([
            'name' => 'Two Piece Suit',
            'slug' => 'two-piece-suit',
            'price' => 4000,
            'currency' => 'PKR',
            'stock_quantity' => 3,
            'piece_count' => 2,
            'included_pieces' => ['kameez', 'dupatta'],
            'piece_lengths' => [
                'kameez' => 40,
                'dupatta' => 2.5,
            ],
            'active' => true,
            'image_urls' => [
                'https://example.com/card-front.jpg',
                'https://example.com/card-back.jpg',
            ],
            'media_paths' => [],
        ]);
        StoreSetting::create(['whatsapp_number' => '923001234567']);

        $message = "Hello, I would like to order this product:\n"
            ."Two Piece Suit\nQuantity: 1\nUnit price: 4,000 PKR\nTotal pieces: 2"
            ."\nIncluded pieces: Kameez (shirt) (40 in), Dupatta (2.5 in)"
            ."\nProduct link: ".route('shop.product', $product);
        $expectedLink = 'href="https://wa.me/923001234567?text='.rawurlencode($message).'"';

        $this->get(route('shop'))
            ->assertOk()
            ->assertSee('productCardGallery'.$product->id, false)
            ->assertSee('Show image 2 of Two Piece Suit', false)
            ->assertSee('product-card-cart-button', false)
            ->assertSee('visually-hidden">Add to cart</span>', false)
            ->assertSee('d-none d-lg-inline">Add</span>', false)
            ->assertSee('d-none d-xl-inline">to cart</span>', false)
            ->assertSee('product-card-whatsapp-button', false)
            ->assertSee('aria-label="Order Two Piece Suit via WhatsApp"', false)
            ->assertSee($expectedLink, false);
    }

    public function test_cart_page_has_a_prefilled_whatsapp_order_link_for_all_items(): void
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
        StoreSetting::create(['whatsapp_number' => '923001234567']);
        $this->withSession([
            'cart' => [
                (string) $product->id => [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $message = "Hello, I would like to place this order:\n"
            ."- Premium Shalwar Kameez (Qty: 2) — 8,000 PKR\n"
            .route('shop.product', $product)
            ."\n\nSubtotal: 8,000 PKR\nDelivery: Free\nTotal: 8,000 PKR";
        $expectedLink = 'href="https://wa.me/923001234567?text='.rawurlencode($message).'"';

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Order via WhatsApp')
            ->assertSee($expectedLink, false);
    }

    public function test_whatsapp_order_button_is_hidden_when_the_saved_number_is_cleared(): void
    {
        config(['store.whatsapp' => '+92 300 1234567']);
        $admin = User::factory()->create();
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

        $this->withSession(['admin_user_id' => $admin->id])
            ->patch(route('settings.update'), ['whatsapp_number' => ''])
            ->assertRedirect(route('settings.edit'));

        $this->get(route('shop.product', $product))
            ->assertOk()
            ->assertDontSee('href="https://wa.me/923001234567', false);
    }
}
