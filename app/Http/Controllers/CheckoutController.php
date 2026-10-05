<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show(Request $request)
    {
        $cart = $this->cart($request);

        if ($cart['items']->isEmpty()) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        return view('storefront.checkout', $cart->toArray());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'customer_email' => ['nullable', 'email', 'max:190'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:120'],
            'shipping_area' => ['nullable', 'string', 'max:160'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! config('store.cod_enabled')) {
            return back()->withErrors(['payment' => 'Cash on Delivery is currently unavailable.']);
        }

        $order = DB::transaction(function () use ($request, $data) {
            $cart = $request->session()->get('cart', []);
            $ids = array_map('intval', array_keys($cart));
            $products = Product::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');

            if ($products->isEmpty()) {
                abort(422, 'Your cart is empty.');
            }

            $subtotal = 0;
            $lines = [];

            foreach ($cart as $productId => $line) {
                $product = $products->get((int) $productId);
                $quantity = (int) ($line['quantity'] ?? 0);

                if (! $product || ! $product->active || $quantity < 1 || $product->stock_quantity < $quantity) {
                    abort(422, 'One or more products are no longer available in the requested quantity.');
                }

                $unitPrice = (float) $product->price;
                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                $lines[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            $shipping = $subtotal > 0 && $subtotal < config('store.free_shipping_threshold')
                ? (float) config('store.shipping_fee')
                : 0;

            $order = Order::create([
                'order_number' => $this->orderNumber(),
                'status' => 'pending',
                'payment_method' => 'cod',
                'payment_status' => 'unpaid',
                'currency' => config('store.currency'),
                'subtotal' => $subtotal,
                'shipping_amount' => $shipping,
                'discount_amount' => 0,
                'total' => $subtotal + $shipping,
                ...$data,
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];
                $image = $product->images()[0] ?? null;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => $line['line_total'],
                    'image_url' => $image && str_starts_with($image, 'http')
                        ? $image
                        : ($image ? asset('storage/'.$image) : null),
                ]);

                $product->decrement('stock_quantity', $line['quantity']);
            }

            return $order->load('items');
        });

        $request->session()->forget('cart');

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        return view('storefront.order-success', compact('order'));
    }

    private function cart(Request $request): object
    {
        $raw = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($raw))->get()->keyBy('id');
        $items = collect();
        $subtotal = 0;

        foreach ($raw as $productId => $line) {
            $product = $products->get((int) $productId);
            if (! $product || ! $product->active || $product->stock_quantity < 1) {
                continue;
            }

            $quantity = min((int) ($line['quantity'] ?? 1), $product->stock_quantity);
            $lineTotal = (float) $product->price * $quantity;
            $subtotal += $lineTotal;

            $items->push(compact('product', 'quantity', 'lineTotal'));
        }

        $shipping = $subtotal > 0 && $subtotal < config('store.free_shipping_threshold')
            ? (float) config('store.shipping_fee')
            : 0;

        return collect([
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
        ]);
    }

    private function orderNumber(): string
    {
        do {
            $number = 'GCH-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
