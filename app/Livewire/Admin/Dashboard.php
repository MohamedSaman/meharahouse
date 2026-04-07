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
    public string $chartPeriod = 'monthly'; // 'daily', 'weekly', 'monthly'

    public function render()
    {
        // KPI stats
        $stats = [
            'total_revenue'  => Order::whereNotIn('status', ['cancelled'])->sum('total'),
            'total_orders'   => Order::count(),
            'pending_orders' => Order::whereIn('status', ['new', 'payment_received'])->count(),
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

        // Revenue chart — adapts to selected period
        $chartData = $this->buildChartData();

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
            'chartData',
            'topProducts',
            'lowStockProducts',
            'orderStatuses'
        ));
    }

    /**
     * Build chart data array based on the selected period.
     * Each entry: ['label' => string, 'revenue' => float, 'count' => int]
     */
    private function buildChartData(): array
    {
        if ($this->chartPeriod === 'daily') {
            // Last 14 days, grouped by date
            $rows = Order::select(
                    DB::raw('DATE(created_at) as period_key'),
                    DB::raw('SUM(total) as revenue'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereNotIn('status', ['cancelled'])
                ->where('created_at', '>=', now()->subDays(13)->startOfDay())
                ->groupBy('period_key')
                ->orderBy('period_key')
                ->get()
                ->keyBy('period_key');

            $data = [];
            for ($i = 13; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $label = now()->subDays($i)->format('M j'); // e.g. "Apr 1"
                $row   = $rows->get($date);
                $data[] = [
                    'label'   => $label,
                    'revenue' => $row ? (float) $row->revenue : 0,
                    'count'   => $row ? (int) $row->count : 0,
                ];
            }
            return $data;
        }

        if ($this->chartPeriod === 'weekly') {
            // Last 8 weeks, grouped by YEARWEEK
            $rows = Order::select(
                    DB::raw('YEARWEEK(created_at, 1) as period_key'),
                    DB::raw('SUM(total) as revenue'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereNotIn('status', ['cancelled'])
                ->where('created_at', '>=', now()->subWeeks(7)->startOfWeek())
                ->groupBy('period_key')
                ->orderBy('period_key')
                ->get()
                ->keyBy('period_key');

            $data = [];
            for ($i = 7; $i >= 0; $i--) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $key       = $weekStart->format('oW'); // ISO year + week number
                $label     = 'W' . $weekStart->format('W'); // e.g. "W14"
                $row       = $rows->get($key);
                $data[] = [
                    'label'   => $label,
                    'revenue' => $row ? (float) $row->revenue : 0,
                    'count'   => $row ? (int) $row->count : 0,
                ];
            }
            return $data;
        }

        // Default: monthly — last 6 months
        $rows = Order::select(
                DB::raw('YEAR(created_at) as yr'),
                DB::raw('MONTH(created_at) as mo'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotIn('status', ['cancelled'])
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('yr', 'mo')
            ->orderBy('yr')
            ->orderBy('mo')
            ->get()
            ->keyBy(fn($r) => $r->yr . '-' . str_pad($r->mo, 2, '0', STR_PAD_LEFT));

        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key   = $month->format('Y-m');
            $label = $month->format('M'); // e.g. "Jan"
            $row   = $rows->get($key);
            $data[] = [
                'label'   => $label,
                'revenue' => $row ? (float) $row->revenue : 0,
                'count'   => $row ? (int) $row->count : 0,
            ];
        }
        return $data;
    }
}
