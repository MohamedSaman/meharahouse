<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

#[Title('Reports')]
#[Layout('layouts.admin')]
class Report extends Component
{
    public string $period = '30'; // days

    public function render()
    {
        $days = (int) $this->period;
        $from = now()->subDays($days);

        // Revenue trend
        $revenueTrend = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->whereNotIn('status', ['cancelled'])
            ->where('created_at', '>=', $from)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top categories by revenue
        $categoryRevenue = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled'])
            ->where('orders.created_at', '>=', $from)
            ->select('categories.name', DB::raw('SUM(order_items.subtotal) as revenue'))
            ->groupBy('categories.name')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        // New customers in period
        $newCustomers = User::where('role', 'customer')
            ->where('created_at', '>=', $from)
            ->count();

        $summary = [
            'revenue'       => Order::whereNotIn('status', ['cancelled'])->where('created_at', '>=', $from)->sum('total'),
            'orders'        => Order::where('created_at', '>=', $from)->count(),
            'new_customers' => $newCustomers,
            'avg_order'     => Order::whereNotIn('status', ['cancelled'])->where('created_at', '>=', $from)->avg('total') ?? 0,
        ];

        return view('livewire.admin.report', compact('revenueTrend', 'categoryRevenue', 'summary'));
    }
}
