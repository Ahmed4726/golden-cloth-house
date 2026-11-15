<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $search = trim($filters['q'] ?? '');

        $customers = Customer::query()
            ->with('latestOrder')
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer): View
    {
        $customer->load('latestOrder')
            ->loadCount('orders')
            ->loadSum('orders', 'total');

        $orders = $customer->orders()
            ->with('items')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('customers.show', compact('customer', 'orders'));
    }
}
