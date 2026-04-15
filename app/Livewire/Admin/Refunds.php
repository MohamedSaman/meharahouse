<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\OrderPayment;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

#[Title('Refunds')]
class Refunds extends Component
{
    use WithPagination;
    use WithFileUploads;

    // ── Filters ───────────────────────────────────────────────────────
    public string $search     = '';
    public string $filterTab  = '';   // '', 'pending', 'processed', 'completed'
    public string $dateFrom   = '';
    public string $dateTo     = '';

    // ── Detail Modal ──────────────────────────────────────────────────
    public bool    $showDetail  = false;
    public ?Refund $selected    = null;

    // ── Payment Modal ─────────────────────────────────────────────────
    public bool   $showPaymentModal   = false;
    public int    $paymentRefundId    = 0;
    public string $paymentMethod      = 'bank_transfer';
    public string $paymentBankAccount = '';
    public string $paymentReference   = '';
    public string $paymentNotes       = '';

    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf|max:5120')]
    public $paymentProofFile = null;

    // ── Watchers ──────────────────────────────────────────────────────

    public function updatedSearch(): void  { $this->resetPage(); }
    public function updatedFilterTab(): void { $this->resetPage(); }
    public function updatedDateFrom(): void  { $this->resetPage(); }
    public function updatedDateTo(): void    { $this->resetPage(); }

    public function clearDates(): void
    {
        $this->dateFrom = '';
        $this->dateTo   = '';
        $this->resetPage();
    }

    // ── Actions ───────────────────────────────────────────────────────

    /**
     * Open the detail modal for a specific refund.
     */
    public function viewRefund(int $id): void
    {
        $this->selected    = Refund::with(['order', 'customer', 'processedBy'])->findOrFail($id);
        $this->showDetail  = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selected   = null;
    }

    /**
     * Mark a refund as completed (money has been received by customer).
     * Kept for backward compatibility; UI now uses processPayment() instead.
     */
    public function markCompleted(int $id): void
    {
        $refund = Refund::findOrFail($id);
        $refund->update(['status' => 'completed']);

        // Refresh selected if the modal is open for this refund
        if ($this->selected && $this->selected->id === $id) {
            $this->selected = Refund::with(['order', 'customer', 'processedBy'])->find($id);
        }

        session()->flash('success', 'Refund marked as completed.');
    }

    // ── Payment Modal ─────────────────────────────────────────────────

    public function openPaymentModal(int $id): void
    {
        $refund = Refund::findOrFail($id);
        $this->paymentRefundId    = $id;
        $this->paymentMethod      = 'bank_transfer';
        $this->paymentBankAccount = $refund->customer_bank_account ?? '';
        $this->paymentReference   = '';
        $this->paymentNotes       = '';
        $this->paymentProofFile   = null;
        $this->showPaymentModal   = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->paymentRefundId  = 0;
        $this->reset(['paymentMethod', 'paymentBankAccount', 'paymentReference', 'paymentNotes', 'paymentProofFile']);
    }

    public function processPayment(): void
    {
        $this->validate([
            'paymentMethod'      => ['required', 'in:bank_transfer,online,cash'],
            'paymentBankAccount' => ['nullable', 'string', 'max:100'],
            'paymentReference'   => ['nullable', 'string', 'max:255'],
            'paymentNotes'       => ['nullable', 'string', 'max:1000'],
            'paymentProofFile'   => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $refund = Refund::with(['order'])->findOrFail($this->paymentRefundId);

        $proofPath = null;
        if ($this->paymentProofFile) {
            $proofPath = $this->paymentProofFile->store('refunds', 'public');
        }

        $refund->update([
            'method'                => $this->paymentMethod,
            'customer_bank_account' => $this->paymentBankAccount ?: null,
            'reference_number'      => $this->paymentReference ?: null,
            'notes'                 => $this->paymentNotes ?: $refund->notes,
            'proof_file'            => $proofPath ?? $refund->proof_file,
            'status'                => 'completed',
            'processed_by'          => auth()->id(),
            'processed_at'          => now(),
        ]);

        // Record the refund as an outflow in OrderPayment for finance tracking
        OrderPayment::create([
            'order_id'     => $refund->order_id,
            'type'         => 'refund',
            'amount'       => $refund->amount,
            'method'       => $this->paymentMethod,
            'reference'    => $this->paymentReference ?: null,
            'notes'        => $this->paymentNotes ?: ('Refund payment to customer' . ($this->paymentBankAccount ? ' — Account: ' . $this->paymentBankAccount : '')),
            'status'       => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        // Capture ID before closePaymentModal() resets paymentRefundId to 0
        $processedId = $this->paymentRefundId;

        $this->closePaymentModal();

        // Refresh selected if the detail modal is still open for this refund
        if ($this->selected && $this->selected->id === $processedId) {
            $this->selected = Refund::with(['order', 'customer', 'processedBy'])->find($processedId);
        }

        session()->flash('success', 'Refund payment processed and marked as completed.');
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        $refunds = Refund::with(['order', 'customer', 'processedBy'])
            ->when($this->search, function ($q) {
                $q->whereHas('order', fn($o) =>
                    $o->where('order_number', 'like', "%{$this->search}%")
                     ->orWhere(
                         DB::raw("JSON_UNQUOTE(JSON_EXTRACT(shipping_address, '$.full_name'))"),
                         'like', "%{$this->search}%"
                     )
                )
                ->orWhereHas('customer', fn($u) =>
                    $u->where('name', 'like', "%{$this->search}%")
                );
            })
            ->when($this->filterTab, fn($q) => $q->where('status', $this->filterTab))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(20);

        $counts = [
            'all'       => Refund::count(),
            'pending'   => Refund::where('status', 'pending')->count(),
            'processed' => Refund::where('status', 'processed')->count(),
            'completed' => Refund::where('status', 'completed')->count(),
        ];

        $layout = auth()->user()?->isAdmin() ? 'layouts.admin' : 'layouts.staff';
        return view('livewire.admin.refunds', compact('refunds', 'counts'))->layout($layout);
    }
}
