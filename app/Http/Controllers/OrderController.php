<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::latest()
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim($request->string('q'));
                $query->where(function ($q) use ($term) {
                    $q->where('order_number', 'like', "%{$term}%")
                        ->orWhere('customer_name', 'like', "%{$term}%")
                        ->orWhere('customer_phone', 'like', "%{$term}%");
                });
            })
            ->paginate(25)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product');

        return view('orders.show', compact('order'));
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
}
