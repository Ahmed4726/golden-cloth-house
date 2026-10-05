<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return view('storefront.track-order');
    }

    public function show(Request $request)
    {
        $data = $request->validate([
            'order_number' => ['required', 'string', 'max:40'],
            'phone' => ['required', 'string', 'max:40'],
        ]);

        $order = Order::with('items')
            ->where('order_number', strtoupper(trim($data['order_number'])))
            ->where('customer_phone', $data['phone'])
            ->first();

        if (! $order) {
            return back()
                ->withInput()
                ->withErrors(['order_number' => 'We could not find an order matching those details.']);
        }

        return view('storefront.track-order', compact('order'));
    }
}
