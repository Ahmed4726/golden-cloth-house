<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlacedMail;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Services\CouponService;
use App\Services\CustomerResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(Request $request, CouponService $coupons): View|RedirectResponse
    {
        $cart = $this->cart($request);

        if ($cart['items']->isEmpty()) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        $couponCode = $request->session()->get('coupon_code');

        if ($couponCode) {
            try {
                $coupon = $coupons->findByCode($couponCode);
                $discount = $coupons->discountFor($coupon, $cart['subtotal']);
                $cart['coupon'] = $coupon;
                $cart['coupon_discount'] = $discount;
                $cart['total'] = $cart['subtotal'] - $discount + $cart['shipping'];
            } catch (ValidationException $exception) {
                $request->session()->forget('coupon_code');

                return redirect()->route('checkout')->withErrors($exception->errors());
            }
        } else {
            $cart['coupon'] = null;
            $cart['coupon_discount'] = 0;
        }

        return view('storefront.checkout', $cart->toArray());
    }

    public function applyCoupon(Request $request, CouponService $coupons): RedirectResponse
    {
        $data = $request->validate([
            'coupon_code' => ['required', 'string', 'max:40'],
        ]);
        $cart = $this->cart($request);

        if ($cart['items']->isEmpty()) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        $coupon = $coupons->findByCode($data['coupon_code']);
        $coupons->discountFor($coupon, $cart['subtotal']);
        $request->session()->put('coupon_code', $coupon->code);

        return redirect()->route('checkout')->with('success', 'Coupon applied.');
    }

    public function removeCoupon(Request $request): RedirectResponse
    {
        $request->session()->forget('coupon_code');

        return redirect()->route('checkout')->with('success', 'Coupon removed.');
    }

    public function store(Request $request, CustomerResolver $customers, CouponService $coupons): RedirectResponse
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
            return back()->withErrors([
                'payment' => 'Cash on Delivery is currently unavailable.',
            ]);
        }

        $order = DB::transaction(function () use ($request, $data, $customers, $coupons) {
            $cart = $request->session()->get('cart', []);
            $ids = array_map('intval', array_keys($cart));
            $products = Product::whereIn('id', $ids)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

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
            $customer = $customers->resolve($data, lockForUpdate: true);
            $coupon = null;
            $discount = 0;
            $couponCode = $request->session()->get('coupon_code');

            if ($couponCode) {
                $coupon = Coupon::query()
                    ->where('code', mb_strtoupper(trim($couponCode)))
                    ->lockForUpdate()
                    ->first();

                if (! $coupon) {
                    $coupon = $coupons->findByCode($couponCode);
                }

                $discount = $coupons->discountFor($coupon, $subtotal, $customer);
                $coupon->increment('usage_count');
            }

            $order = Order::create([
                'order_number' => $this->orderNumber(),
                'customer_id' => $customer?->id,
                'status' => 'pending',
                'payment_method' => 'cod',
                'payment_status' => 'unpaid',
                'currency' => config('store.currency'),
                'subtotal' => $subtotal,
                'shipping_amount' => $shipping,
                'discount_amount' => $discount,
                'total' => $subtotal - $discount + $shipping,
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
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
        $request->session()->forget('coupon_code');

        if ($order->customer_email) {
            Mail::to($order->customer_email)->queue(new OrderPlacedMail($order));
        }

        return redirect()->route('checkout.success', ['order' => $order->order_number]);
    }

    public function success(Order $order): View
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
