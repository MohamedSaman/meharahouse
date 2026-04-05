<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

#[Title('Dashboard')]
#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function render()
    {
        // KPI stats
        $stats = [
            'total_revenue'  => Order::whereNotIn('status', ['cancelled'])->sum('total'),
            'total_orders'   => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_products' => Product::count(),
            'total_users'    => User::where('role', 'customer')->count(),
            'low_stock'      => Product::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock'   => Product::where('stock', 0)->count(),
        ];

        // Recent orders (last 10)
        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Revenue by month (last 6 months) for chart
        $revenueChart = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotIn('status', ['cancelled'])
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Top selling products
        $topProducts = Product::withSum(['orderItems as total_sold' => function ($q) {
                $q->join('orders', 'order_items.order_id', '=', 'orders.id')
                  ->whereNotIn('orders.status', ['cancelled']);
            }], 'quantity')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Low stock alerts
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(8)
            ->get();

        // Order status breakdown
        $orderStatuses = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return view('livewire.admin.dashboard', compact(
            'stats',
            'recentOrders',
            'revenueChart',
            'topProducts',
            'lowStockProducts',
            'orderStatuses'
        ));
    }
}
