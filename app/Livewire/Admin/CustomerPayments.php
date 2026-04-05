<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\CustomerAccount;
use App\Models\CustomerPaymentRecord;
use App\Models\Order;

#[Title('Customer Payments')]
#[Layout('layouts.admin')]
class CustomerPayments extends Component
{
    use WithPagination;

    // ── List / Filter ──────────────────────────────────────────────────────
    public string $search       = '';
    public string $filterStatus = '';
    public string $sortField    = 'created_at';
    public string $sortDir      = 'desc';

    // Expanded row (show payment history inline)
    public ?int $expandedAccountId = null;

    // ── Add Account Modal ──────────────────────────────────────────────────
    public bool   $showAccountModal = false;
    public string $orderId          = '';
    public string $customerName     = '';
    public string $customerPhone    = '';
    public string $customerEmail    = '';
    public string $totalAmount      = '';
    public string $accountNotes     = '';

    // ── Receive Payment Modal ──────────────────────────────────────────────
    public bool   $showReceiveModal   = false;
    public ?int   $selectedAccountId  = null;
    public string $receiveAmount      = '';
    public string $receiveType        = 'payment';
    public string $receiveMethod      = 'cash';
    public string $receiveReference   = '';
    public string $receiveDate        = '';
    public string $receiveNotes       = '';

    // ── Lifecycle ──────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->receiveDate = now()->format('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    // ── When an order is selected, auto-fill total amount ──────────────────

    public function updatedOrderId(string $value): void
    {
        if ($value && is_numeric($value)) {
            $order = Order::find($value);
            if ($order) {
                $this->totalAmount  = number_format((float) $order->total, 2, '.', '');
                $this->customerName = $order->user?->name ?? $this->customerName;
            }
        }
    }

    // ── Expand row ─────────────────────────────────────────────────────────

    public function toggleRow(int $accountId): void
    {
        $this->expandedAccountId = ($this->expandedAccountId === $accountId) ? null : $accountId;
    }

    // ── Create Account ─────────────────────────────────────────────────────

    public function openAccountModal(): void
    {
        $this->resetAccountModal();
        $this->showAccountModal = true;
    }

    public function createAccount(): void
    {
        $this->validate([
            'customerName'  => 'required|string|max:255',
            'customerPhone' => 'nullable|string|max:30',
            'customerEmail' => 'nullable|email|max:255',
            'orderId'       => 'nullable|exists:orders,id',
            'totalAmount'   => 'required|numeric|min:0.01',
            'accountNotes'  => 'nullable|string|max:1000',
        ]);

        $total = (float) $this->totalAmount;

        CustomerAccount::create([
            'order_id'       => $this->orderId ?: null,
            'customer_name'  => $this->customerName,
            'customer_phone' => $this->customerPhone ?: null,
            'customer_email' => $this->customerEmail ?: null,
            'total_amount'   => $total,
            'paid_amount'    => 0,
            'due_amount'     => $total,
            'status'         => 'pending',
            'notes'          => $this->accountNotes ?: null,
        ]);

        session()->flash('success', 'Customer payment account created successfully.');
        $this->resetAccountModal();
        $this->showAccountModal = false;
    }

    private function resetAccountModal(): void
    {
        $this->orderId        = '';
        $this->customerName   = '';
        $this->customerPhone  = '';
        $this->customerEmail  = '';
        $this->totalAmount    = '';
        $this->accountNotes   = '';
        $this->resetErrorBag();
    }

    // ── Receive Payment Modal ──────────────────────────────────────────────

    public function openReceiveModal(int $accountId): void
    {
        $account = CustomerAccount::findOrFail($accountId);

        $this->selectedAccountId = $accountId;
        $this->receiveAmount     = number_format((float) $account->due_amount, 2, '.', '');
        $this->receiveType       = 'payment';
        $this->receiveMethod     = 'cash';
        $this->receiveReference  = '';
        $this->receiveDate       = now()->format('Y-m-d');
        $this->receiveNotes      = '';
        $this->showReceiveModal  = true;
        $this->resetErrorBag();
    }

    public function recordReceipt(): void
    {
        $this->validate([
            'selectedAccountId' => 'required|exists:customer_accounts,id',
            'receiveAmount'     => 'required|numeric|min:0.01',
            'receiveType'       => 'required|in:advance,payment',
            'receiveMethod'     => 'required|in:cash,bank_transfer,mobile_money,telebirr,cbebirr',
            'receiveReference'  => 'nullable|string|max:255',
            'receiveDate'       => 'required|date',
            'receiveNotes'      => 'nullable|string|max:1000',
        ]);

        $account = CustomerAccount::findOrFail($this->selectedAccountId);

        CustomerPaymentRecord::create([
            'customer_account_id' => $account->id,
            'amount'              => $this->receiveAmount,
            'payment_type'        => $this->receiveType,
            'payment_method'      => $this->receiveMethod,
            'reference'           => $this->receiveReference ?: null,
            'paid_at'             => $this->receiveDate,
            'notes'               => $this->receiveNotes ?: null,
        ]);

        // Recalculate totals and status on the account
        $account->recalculate();

        session()->flash('success', 'Payment received and recorded successfully.');
        $this->showReceiveModal  = false;
        $this->selectedAccountId = null;
    }

    // ── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
        $accounts = CustomerAccount::with('order')
            ->when($this->search, function ($q) {
                $term = "%{$this->search}%";
                $q->where('customer_name', 'like', $term)
                  ->orWhere('customer_phone', 'like', $term)
                  ->orWhere('customer_email', 'like', $term)
                  ->orWhereHas('order', fn($o) => $o->where('order_number', 'like', $term));
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate(20);

        // Expanded row payment history
        $expandedPayments = null;
        if ($this->expandedAccountId) {
            $expandedPayments = CustomerPaymentRecord::where('customer_account_id', $this->expandedAccountId)
                ->orderBy('paid_at', 'desc')
                ->get();
        }

        // Stats
        $statsQuery = CustomerAccount::query()
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus));

        $stats = [
            'total_receivable'  => (float) $statsQuery->sum('total_amount'),
            'total_received'    => (float) $statsQuery->sum('paid_amount'),
            'total_outstanding' => (float) $statsQuery->sum('due_amount'),
        ];

        // Recent orders for the "link order" select (limit to last 100 for perf)
        $recentOrders = Order::orderBy('created_at', 'desc')
            ->limit(100)
            ->get(['id', 'order_number', 'total']);

        return view('livewire.admin.customer-payments', compact(
            'accounts',
            'expandedPayments',
            'stats',
            'recentOrders',
        ));
    }
}
