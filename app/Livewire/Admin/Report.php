<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SupplierPaymentRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

#[Title('Reports')]
#[Layout('layouts.admin')]
class Report extends Component
{
    public string $activeTab = 'sales';   // sales | finance | stock | expenses | profit
    public string $dateFrom  = '';        // defaults to first day of current month
    public string $dateTo    = '';        // defaults to today
    public string $period    = 'custom';  // quick preset: 7d | 30d | 90d | year | custom

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
    }

    /**
     * When a quick-period preset is selected, auto-populate the date range.
     */
    public function updatedPeriod(string $value): void
    {
        $today = now()->format('Y-m-d');

        match ($value) {
            'today' => [$this->dateFrom, $this->dateTo] = [$today, $today],
            '7d'    => [$this->dateFrom, $this->dateTo] = [now()->subDays(6)->format('Y-m-d'),    $today],
            '30d'   => [$this->dateFrom, $this->dateTo] = [now()->subDays(29)->format('Y-m-d'),   $today],
            '90d'   => [$this->dateFrom, $this->dateTo] = [now()->subDays(89)->format('Y-m-d'),   $today],
            'year'  => [$this->dateFrom, $this->dateTo] = [now()->startOfYear()->format('Y-m-d'), $today],
            default => null, // 'custom' — leave the dates as-is
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EXPORT CSV
    // ─────────────────────────────────────────────────────────────────────────

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $tab      = $this->activeTab;
        $dateFrom = $this->dateFrom;
        $dateTo   = $this->dateTo;
        $filename = 'meharahouse-' . $tab . '-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($tab, $dateFrom, $dateTo) {
            $handle = fopen('php://output', 'w');

            match ($tab) {
                'sales'    => $this->exportSalesCsv($handle, $dateFrom, $dateTo),
                'finance'  => $this->exportFinanceCsv($handle, $dateFrom, $dateTo),
                'stock'    => $this->exportStockCsv($handle),
                'expenses' => $this->exportExpensesCsv($handle, $dateFrom, $dateTo),
                'profit'   => $this->exportProfitCsv($handle, $dateFrom, $dateTo),
                default    => $this->exportSalesCsv($handle, $dateFrom, $dateTo),
            };

            fclose($handle);
        }, $filename);
    }

    private function exportSalesCsv($handle, string $dateFrom, string $dateTo): void
    {
        fputcsv($handle, ['Order #', 'Date', 'Customer', 'Items', 'Subtotal', 'Tax', 'Shipping', 'Discount', 'Total', 'Status', 'Payment Method']);

        $orders = Order::with(['user', 'items'])
            ->whereNotIn('status', ['cancelled'])
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->get();

        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->order_number,
                $order->created_at->format('Y-m-d'),
                $order->user?->name ?? 'Guest',
                $order->items->sum('quantity'),
                $order->subtotal,
                $order->tax,
                $order->shipping_cost,
                $order->discount,
                $order->total,
                $order->status,
                $order->payment_method ?? '',
            ]);
        }
    }

    private function exportFinanceCsv($handle, string $dateFrom, string $dateTo): void
    {
        fputcsv($handle, ['Order #', 'Date', 'Gross Revenue', 'Discount', 'Tax', 'Shipping', 'Net Total', 'Payment Method', 'Status']);

        $orders = Order::whereNotIn('status', ['cancelled'])
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->get();

        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->order_number,
                $order->created_at->format('Y-m-d'),
                $order->subtotal,
                $order->discount,
                $order->tax,
                $order->shipping_cost,
                $order->total,
                $order->payment_method ?? '',
                $order->status,
            ]);
        }
    }

    private function exportStockCsv($handle): void
    {
        fputcsv($handle, ['Product Name', 'SKU', 'Category', 'Stock', 'Price', 'Cost Price', 'Stock Value', 'Status']);

        $products = Product::with('category')->orderBy('stock')->get();

        foreach ($products as $product) {
            $stockValue = $product->stock * ($product->cost_price ?? 0);
            fputcsv($handle, [
                $product->name,
                $product->sku,
                $product->category?->name ?? '-',
                $product->stock,
                $product->price,
                $product->cost_price ?? 0,
                number_format($stockValue, 2),
                $product->stock === 0 ? 'Out of Stock' : ($product->stock <= 10 ? 'Low Stock' : 'In Stock'),
            ]);
        }
    }

    private function exportExpensesCsv($handle, string $dateFrom, string $dateTo): void
    {
        fputcsv($handle, ['PO #', 'Date', 'Supplier', 'Status', 'Subtotal', 'Shipping Cost', 'Total']);

        $purchaseOrders = PurchaseOrder::with('supplier')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->whereIn('status', ['received', 'partial', 'ordered'])
            ->orderByDesc('created_at')
            ->get();

        foreach ($purchaseOrders as $po) {
            fputcsv($handle, [
                $po->po_number,
                $po->created_at->format('Y-m-d'),
                $po->supplier?->name ?? '-',
                $po->statusLabel(),
                $po->subtotal,
                $po->shipping_cost,
                $po->total,
            ]);
        }
    }

    private function exportProfitCsv($handle, string $dateFrom, string $dateTo): void
    {
        // Recompute profit figures for the export
        $financeOrders = Order::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $supplierPayments = SupplierPaymentRecord::whereBetween('paid_at', [$dateFrom, $dateTo])->get();

        $cogs = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->sum(DB::raw('order_items.quantity * COALESCE(products.cost_price, 0)'));

        $revenue      = $financeOrders->whereIn('status', ['delivered', 'completed'])->sum('total');
        $grossProfit  = $revenue - $cogs;
        $totalExpenses = $supplierPayments->sum('amount');
        $netProfit    = $grossProfit - $totalExpenses;

        fputcsv($handle, ['Metric', 'Amount']);
        fputcsv($handle, ['Revenue (Delivered + Completed)', number_format($revenue, 2)]);
        fputcsv($handle, ['Cost of Goods Sold (COGS)', number_format((float) $cogs, 2)]);
        fputcsv($handle, ['Gross Profit', number_format($grossProfit, 2)]);
        fputcsv($handle, ['Gross Margin %', ($revenue > 0 ? round(($grossProfit / $revenue) * 100, 1) : 0) . '%']);
        fputcsv($handle, ['Total Supplier Payments (Expenses)', number_format($totalExpenses, 2)]);
        fputcsv($handle, ['Net Profit', number_format($netProfit, 2)]);
        fputcsv($handle, ['Net Margin %', ($revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0) . '%']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RENDER
    // ─────────────────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $dateFrom = $this->dateFrom ?: now()->startOfMonth()->format('Y-m-d');
        $dateTo   = $this->dateTo   ?: now()->format('Y-m-d');

        // ── SALES ──────────────────────────────────────────────────────────
        $salesOrders = Order::with('items.product')
            ->whereNotIn('status', ['cancelled'])
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->get();

        $salesSummary = [
            'total_revenue' => $salesOrders->sum('total'),
            'total_orders'  => $salesOrders->count(),
            'avg_order'     => $salesOrders->avg('total') ?? 0,
            'total_items'   => $salesOrders->sum(fn($o) => $o->items->sum('quantity')),
        ];

        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled'])
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->select(
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as qty_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->groupBy('order_items.product_name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $ordersByStatus = Order::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $salesByDay = Order::whereNotIn('status', ['cancelled'])
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── FINANCE ────────────────────────────────────────────────────────
        $financeOrders = Order::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $paymentsConfirmed = OrderPayment::join('orders', 'order_payments.order_id', '=', 'orders.id')
            ->where('order_payments.status', 'confirmed')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->sum('order_payments.amount');

        $financeSummary = [
            'gross_revenue'  => $financeOrders->sum('total'),
            'paid_amount'    => (float) $paymentsConfirmed,
            'pending_amount' => $financeOrders->sum('total') - (float) $paymentsConfirmed,
            'total_discount' => $financeOrders->sum('discount'),
            'total_tax'      => $financeOrders->sum('tax'),
            'total_shipping' => $financeOrders->sum('shipping_cost'),
        ];

        $paymentMethods = Order::whereNotIn('status', ['cancelled'])
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('payment_method')
            ->get();

        // ── STOCK ──────────────────────────────────────────────────────────
        $allProducts = Product::with('category')->get();

        $stockSummary = [
            'total_products' => $allProducts->count(),
            'in_stock'       => $allProducts->where('stock', '>', 10)->count(),
            'low_stock'      => $allProducts->whereBetween('stock', [1, 10])->count(),
            'out_of_stock'   => $allProducts->where('stock', 0)->count(),
            'total_value'    => $allProducts->sum(fn($p) => $p->stock * ($p->cost_price ?? 0)),
        ];

        $lowStockProducts = $allProducts->where('stock', '<=', 10)->sortBy('stock')->values();

        // ── EXPENSES ───────────────────────────────────────────────────────
        $purchaseOrders = PurchaseOrder::with('supplier')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->whereIn('status', ['received', 'partial', 'ordered'])
            ->orderByDesc('created_at')
            ->get();

        $supplierPayments = SupplierPaymentRecord::whereBetween('paid_at', [$dateFrom, $dateTo])->get();

        $expensesSummary = [
            'total_purchases'  => $purchaseOrders->sum('total'),
            'total_paid'       => $supplierPayments->sum('amount'),
            'purchase_orders'  => $purchaseOrders->count(),
        ];

        // ── PROFIT ─────────────────────────────────────────────────────────
        $cogs = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->sum(DB::raw('order_items.quantity * COALESCE(products.cost_price, 0)'));

        $revenue      = $financeOrders->whereIn('status', ['delivered', 'completed'])->sum('total');
        $grossProfit  = $revenue - $cogs;
        $totalExpenses = $supplierPayments->sum('amount');
        $netProfit    = $grossProfit - $totalExpenses;

        $profitSummary = [
            'revenue'      => $revenue,
            'cogs'         => (float) $cogs,
            'gross_profit' => $grossProfit,
            'gross_margin' => $revenue > 0 ? round(($grossProfit / $revenue) * 100, 1) : 0,
            'expenses'     => $totalExpenses,
            'net_profit'   => $netProfit,
            'net_margin'   => $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0,
        ];

        return view('livewire.admin.report', compact(
            'salesSummary',
            'topProducts',
            'ordersByStatus',
            'salesByDay',
            'financeSummary',
            'paymentMethods',
            'stockSummary',
            'lowStockProducts',
            'allProducts',
            'expensesSummary',
            'purchaseOrders',
            'profitSummary',
        ));
    }
}
