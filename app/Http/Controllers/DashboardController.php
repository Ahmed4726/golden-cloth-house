<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\SocialConnection;
use App\Models\SocialPost;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'connections' => SocialConnection::latest()->get(),
            'posts' => SocialPost::withCount('publications')->latest()->limit(8)->get(),
            'recentOrders' => Order::latest()->limit(8)->get(),
            'stats' => [
                'connections' => SocialConnection::where('active', true)->count(),
                'drafts' => SocialPost::where('status', 'draft')->count(),
                'scheduled' => SocialPost::where('status', 'scheduled')->count(),
                'published' => SocialPost::where('status', 'published')->count(),
                'products' => Product::where('active', true)->count(),
                'orders' => Order::count(),
                'pendingOrders' => Order::whereIn('status', ['pending', 'confirmed', 'processing'])->count(),
                'revenue' => Order::where('status', '!=', 'cancelled')->sum('total'),
            ],
        ]);
    }
}
