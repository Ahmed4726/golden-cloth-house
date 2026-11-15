<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\CustomerResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,confirmed,processing,shipped,delivered,cancelled'],
        ]);
        $search = trim($filters['q'] ?? '');

        $orders = Order::latest()
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            })
            ->paginate(25)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'customer');

        return view('orders.show', compact('order'));
    }

    public function createSale(Product $product): View
    {
        return view('orders.create-sale', compact('product'));
    }

    public function storeSale(Request $request, Product $product, CustomerResolver $customers): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'customer_email' => ['nullable', 'email', 'max:190'],
            'shipping_address' => ['nullable', 'string', 'max:500'],
            'shipping_city' => ['nullable', 'string', 'max:120'],
            'shipping_area' => ['nullable', 'string', 'max:160'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $order = DB::transaction(function () use ($data, $product, $customers) {
            $soldProduct = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();
            $quantity = (int) $data['quantity'];

            if ($soldProduct->stock_quantity < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Only '.$soldProduct->stock_quantity.' item(s) are currently in stock.',
                ]);
            }

            $unitPrice = (float) $soldProduct->price;
            $lineTotal = round($unitPrice * $quantity, 2);
            $now = now();
            $customer = $customers->resolve($data);
            $order = Order::create([
                'order_number' => $this->inStoreOrderNumber(),
                'customer_id' => $customer?->id,
                'status' => 'delivered',
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'currency' => $soldProduct->currency,
                'subtotal' => $lineTotal,
                'shipping_amount' => 0,
                'discount_amount' => 0,
                'total' => $lineTotal,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'shipping_address' => $data['shipping_address'] ?? null,
                'shipping_city' => $data['shipping_city'] ?? null,
                'shipping_area' => $data['shipping_area'] ?? null,
                'customer_note' => $data['customer_note'] ?? null,
                'delivered_at' => $now,
            ]);

            $image = $soldProduct->images()[0] ?? null;
            $order->items()->create([
                'product_id' => $soldProduct->id,
                'product_name' => $soldProduct->name,
                'sku' => $soldProduct->sku,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'image_url' => $image && str_starts_with($image, 'http')
                    ? $image
                    : ($image ? asset('storage/'.$image) : null),
            ]);

            $soldProduct->decrement('stock_quantity', $quantity);

            return $order;
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'In-store sale recorded and stock updated.');
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,shipped,delivered,cancelled'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($order, $data) {
            $previous = $order->status;
            $next = $data['status'];

            if ($previous !== 'cancelled' && $next === 'cancelled') {
                foreach ($order->items()->with('product')->get() as $item) {
                    if ($item->product) {
                        $item->product->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            if ($previous === 'cancelled' && $next !== 'cancelled') {
                foreach ($order->items()->with('product')->get() as $item) {
                    if (! $item->product || $item->product->stock_quantity < $item->quantity) {
                        abort(422, 'This order cannot be reopened because stock is insufficient.');
                    }
                }

                foreach ($order->items()->with('product')->get() as $item) {
                    $item->product->decrement('stock_quantity', $item->quantity);
                }
            }

            $timestamps = [];
            if ($next === 'confirmed' && ! $order->confirmed_at) {
                $timestamps['confirmed_at'] = now();
            }
            if ($next === 'shipped') {
                $timestamps['shipped_at'] = $order->shipped_at ?: now();
            }
            if ($next === 'delivered') {
                $timestamps['delivered_at'] = $order->delivered_at ?: now();
            }

            $order->update([
                'status' => $next,
                'admin_note' => $data['admin_note'] ?? null,
                ...$timestamps,
            ]);
        });

        return back()->with('success', 'Order updated successfully.');
    }

    private function inStoreOrderNumber(): string
    {
        do {
            $number = 'GCH-POS-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
