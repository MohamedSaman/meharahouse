<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Support\Facades\DB;

#[Title('Payments')]
#[Layout('layouts.admin')]
class Payment extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterMethod = '';

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }
    public function updatedFilterMethod(): void { $this->resetPage(); }

    public function render()
    {
        $payments = Order::with(['user', 'payments' => fn($q) => $q->where('status', 'confirmed')])
            ->when($this->search, fn($q) => $q
                ->where('order_number', 'like', "%{$this->search}%")
                ->orWhere(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(shipping_address, '$.full_name'))"), 'like', "%{$this->search}%")
                ->orWhere(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(shipping_address, '$.phone'))"), 'like', "%{$this->search}%"))
            ->when($this->filterStatus, function ($q) {
                if ($this->filterStatus === 'partial') {
                    // Orders with advance paid but balance still due
                    $q->where('payment_status', 'pending')
                      ->where('advance_amount', '>', 0)
                      ->where('balance_amount', '>', 0);
                } else {
                    $q->where('payment_status', $this->filterStatus);
                }
            })
            ->when($this->filterMethod, fn($q) => $q->where('payment_method', $this->filterMethod))
            ->latest()
            ->paginate(20);

        // Total confirmed payments received (from order_payments table)
        $totalConfirmedPayments = OrderPayment::where('status', 'confirmed')
            ->whereIn('type', ['advance', 'balance'])
            ->sum('amount');

        // Total pending (unconfirmed) payment receipts
        $totalPendingReceipts = OrderPayment::where('status', 'pending')->sum('amount');

        // Orders with partial payment (advance paid, balance outstanding)
        $partialCount = Order::where('payment_status', 'pending')
            ->where('advance_amount', '>', 0)
            ->where('balance_amount', '>', 0)
            ->count();

        // Total balance still due across all orders
        $totalDue = Order::where('payment_status', '!=', 'paid')
            ->where('balance_amount', '>', 0)
            ->sum('balance_amount');

        // Also subtract any confirmed balance payments already made
        $confirmedBalancePayments = OrderPayment::where('status', 'confirmed')
            ->where('type', 'balance')
            ->sum('amount');
        $totalDue = max(0, $totalDue - $confirmedBalancePayments);

        $summary = [
            'total_collected'       => $totalConfirmedPayments,
            'total_pending_receipts'=> $totalPendingReceipts,
            'partial_count'         => $partialCount,
            'total_due'             => $totalDue,
            'total_orders'          => Order::count(),
            // Quick-tab counts
            'count_paid'            => Order::where('payment_status', 'paid')->count(),
            'count_pending'         => Order::where('payment_status', 'pending')->count(),
            'count_failed'          => Order::where('payment_status', 'failed')->count(),
            'count_refunded'        => Order::where('payment_status', 'refunded')->count(),
        ];

        return view('livewire.admin.payment', compact('payments', 'summary'));
    }
}
