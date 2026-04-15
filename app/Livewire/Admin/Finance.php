<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Refund;
use App\Models\PurchaseOrder;
use App\Models\ShipmentBatch;
use App\Models\OrderReturn;
use App\Models\OrderBackorder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

#[Title('Finance')]
#[Layout('layouts.admin')]
class Finance extends Component
{
    public string $dateFrom    = '';
    public string $dateTo      = '';
    public string $quickFilter = 'this_month'; // today, this_week, this_month, this_year, custom

    public function mount(): void
    {
        $this->applyQuickFilter('this_month');
    }

    public function applyQuickFilter(string $filter): void
    {
        $this->quickFilter = $filter;
        $now = Carbon::now();
        switch ($filter) {
            case 'today':
                $this->dateFrom = $now->copy()->startOfDay()->toDateString();
                $this->dateTo   = $now->copy()->endOfDay()->toDateString();
                break;
            case 'this_week':
                $this->dateFrom = $now->copy()->startOfWeek()->toDateString();
                $this->dateTo   = $now->copy()->endOfWeek()->toDateString();
                break;
            case 'this_month':
                $this->dateFrom = $now->copy()->startOfMonth()->toDateString();
                $this->dateTo   = $now->copy()->endOfMonth()->toDateString();
                break;
            case 'this_year':
                $this->dateFrom = $now->copy()->startOfYear()->toDateString();
                $this->dateTo   = $now->copy()->endOfYear()->toDateString();
                break;
            // 'custom' — user sets dateFrom/dateTo manually via wire:model.live
        }
    }

    public function updatedDateFrom(): void
    {
        $this->quickFilter = 'custom';
    }

    public function updatedDateTo(): void
    {
        $this->quickFilter = 'custom';
    }

    public function render()
    {
        $from = $this->dateFrom
            ? Carbon::parse($this->dateFrom)->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();

        $to = $this->dateTo
            ? Carbon::parse($this->dateTo)->endOfDay()
            : Carbon::now()->endOfDay();

        // ── Sales (orders created in period, not cancelled) ───────────────
        $salesOrders = Order::whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('COUNT(*) as count, SUM(total) as total, SUM(advance_amount) as advance_total, SUM(balance_amount) as balance_total')
            ->first();

        // ── Collected payments (confirmed in period) ──────────────────────
        // Exclude type='refund' — those are outflows already counted in $refundsData.
        // Including them here would double-count them and inflate income.
        $collected = OrderPayment::whereBetween('confirmed_at', [$from, $to])
            ->where('status', 'confirmed')
            ->where('type', '!=', 'refund')
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();

        $collectedByType = OrderPayment::whereBetween('confirmed_at', [$from, $to])
            ->where('status', 'confirmed')
            ->where('type', '!=', 'refund')
            ->selectRaw('type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        // ── Outstanding due (active orders in period with unpaid balance) ──
        $outstanding = Order::whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->whereIn('payment_status', ['pending', 'partial'])
            ->selectRaw('COUNT(*) as count, SUM(balance_amount) as total')
            ->first();

        // ── Refunds ───────────────────────────────────────────────────────
        $refundsData = Refund::whereBetween(DB::raw('COALESCE(processed_at, created_at)'), [$from, $to])
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();

        // ── Supplier costs (purchase orders in period) ────────────────────
        $supplierCosts = PurchaseOrder::whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->selectRaw('SUM(total) as total, COUNT(*) as count')
            ->first();

        // ── Shipment / Courier costs ──────────────────────────────────────
        $shipmentCosts = ShipmentBatch::whereBetween('created_at', [$from, $to])
            ->selectRaw('SUM(courier_cost) as total, COUNT(*) as count')
            ->first();

        // ── Returns ───────────────────────────────────────────────────────
        $returnsData = OrderReturn::whereBetween('created_at', [$from, $to])
            ->selectRaw('COUNT(*) as count')
            ->first();

        // ── Profit estimate ───────────────────────────────────────────────
        // BUG-14 fix: Include product cost prices (COGS) for a more accurate profit.
        // Calculate COGS from completed/delivered order items that have a product with cost_price.
        $cogsSql = \App\Models\OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('orders.status', ['completed', 'delivered', 'dispatched'])
            ->whereBetween('orders.created_at', [$from, $to])
            ->whereNotNull('products.cost_price')
            ->where('products.cost_price', '>', 0)
            ->selectRaw('SUM(products.cost_price * order_items.quantity) as total_cogs')
            ->first();
        $totalCogs = (float) ($cogsSql->total_cogs ?? 0);

        $totalCollected  = (float) ($collected->total ?? 0);
        $totalCosts      = (float) ($supplierCosts->total ?? 0)
                         + (float) ($shipmentCosts->total ?? 0)
                         + (float) ($refundsData->total ?? 0);
        // Use the higher of supplier purchase costs or COGS to avoid double-counting
        $effectiveCogs   = max($totalCogs, (float) ($supplierCosts->total ?? 0));
        $totalCostsWithCogs = $effectiveCogs
                         + (float) ($shipmentCosts->total ?? 0)
                         + (float) ($refundsData->total ?? 0);
        $estimatedProfit = $totalCollected - $totalCostsWithCogs;

        // ── Orders by status breakdown ────────────────────────────────────
        $ordersByStatus = Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as count, SUM(total) as total')
            ->groupBy('status')
            ->orderByRaw("FIELD(status, 'completed','delivered','dispatched','sourcing','confirmed','payment_received','new','refunded','cancelled')")
            ->get();

        // ── Recent refunds list ───────────────────────────────────────────
        $recentRefunds = Refund::with('order')
            ->whereBetween(DB::raw('COALESCE(processed_at, created_at)'), [$from, $to])
            ->latest()
            ->limit(10)
            ->get();

        // ── Recent purchase orders ────────────────────────────────────────
        $recentPurchaseOrders = PurchaseOrder::with('supplier')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', ['cancelled'])
            ->latest()
            ->limit(10)
            ->get();

        // ── Recent payments ───────────────────────────────────────────────
        $recentPayments = OrderPayment::with('order')
            ->whereBetween('confirmed_at', [$from, $to])
            ->where('status', 'confirmed')
            ->latest('confirmed_at')
            ->limit(10)
            ->get();

        return view('livewire.admin.finance', compact(
            'salesOrders', 'collected', 'collectedByType',
            'outstanding', 'refundsData', 'supplierCosts',
            'shipmentCosts', 'returnsData', 'estimatedProfit',
            'totalCollected', 'totalCosts',
            'ordersByStatus', 'recentRefunds', 'recentPurchaseOrders', 'recentPayments'
        ));
    }
}
