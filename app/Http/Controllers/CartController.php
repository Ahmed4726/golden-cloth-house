<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        return view('storefront.cart', $this->cartData($request));
    }

    public function add(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        abort_unless($product->active, 404);

        $cart = $request->session()->get('cart', []);
        $key = (string) $product->id;
        $quantity = ($cart[$key]['quantity'] ?? 0) + $data['quantity'];

        if (! $product->isInStock($quantity)) {
            return back()->withErrors([
                'quantity' => 'The requested quantity is not available.',
            ]);
        }

        $cart[$key] = [
            'product_id' => $product->id,
            'quantity' => $quantity,
        ];

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Product added to your cart.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'quantities' => ['nullable', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0', 'max:20'],
            'remove_product' => ['nullable', 'integer'],
        ]);

        $cart = $request->session()->get('cart', []);

        if (! empty($data['remove_product'])) {
            unset($cart[(string) $data['remove_product']]);
            $request->session()->put('cart', $cart);

            return back()->with('success', 'Product removed from your cart.');
        }

        foreach ($data['quantities'] ?? [] as $productId => $quantity) {
            $key = (string) $productId;

            if (! isset($cart[$key])) {
                continue;
            }

            if ((int) $quantity === 0) {
                unset($cart[$key]);
                continue;
            }

            $product = Product::find($productId);

            if (! $product || ! $product->isInStock((int) $quantity)) {
                return back()->withErrors([
                    'cart' => 'One or more products are unavailable in the requested quantity.',
                ]);
            }

            $cart[$key]['quantity'] = (int) $quantity;
        }

        $request->session()->put('cart', $cart);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, Product $product)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[(string) $product->id]);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Product removed from your cart.');
    }

    private function cartData(Request $request): array
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $items = [];
        $subtotal = 0;

        foreach ($cart as $key => $line) {
            $product = $products->get((int) $key);

            if (! $product || ! $product->active) {
                continue;
            }

            $quantity = min((int) ($line['quantity'] ?? 1), $product->stock_quantity);
            if ($quantity < 1) {
                continue;
            }

            $lineTotal = (float) $product->price * $quantity;
            $subtotal += $lineTotal;
            $items[] = compact('product', 'quantity', 'lineTotal');
        }

        $shipping = $subtotal > 0 && $subtotal < config('store.free_shipping_threshold')
            ? config('store.shipping_fee')
            : 0;

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
            'itemCount' => array_sum(array_map(fn ($item) => $item['quantity'], $items)),
        ];
    }
}
