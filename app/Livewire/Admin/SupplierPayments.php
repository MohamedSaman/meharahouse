<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\SupplierPaymentRecord;

#[Title('Supplier Payments')]
#[Layout('layouts.admin')]
class SupplierPayments extends Component
{
    use WithPagination;

    // ── List / Filter ──────────────────────────────────────────────────────
    public string $search      = '';
    public string $filterStatus = '';
    public string $sortField   = 'created_at';
    public string $sortDir     = 'desc';

    // Expanded row (show payment history inline)
    public ?int $expandedInvoiceId = null;

    // ── Add Invoice Modal ──────────────────────────────────────────────────
    public bool   $showInvoiceModal = false;
    public string $supplierId       = '';
    public string $invoiceNumber    = '';
    public string $invoiceDate      = '';
    public string $totalAmount      = '';
    public string $invoiceNotes     = '';

    // Quick-add supplier inline
    public bool   $showNewSupplierForm = false;
    public string $newSupplierName     = '';
    public string $newSupplierPhone    = '';

    // ── Pay Modal ──────────────────────────────────────────────────────────
    public bool    $showPayModal       = false;
    public ?int    $selectedInvoiceId  = null;
    public string  $payAmount          = '';
    public string  $payMethod          = 'cash';
    public string  $payReference       = '';
    public string  $payDate            = '';
    public string  $payNotes           = '';

    // ── Lifecycle ──────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->invoiceDate = now()->format('Y-m-d');
        $this->payDate     = now()->format('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    // ── Expand row ─────────────────────────────────────────────────────────

    public function toggleRow(int $invoiceId): void
    {
        $this->expandedInvoiceId = ($this->expandedInvoiceId === $invoiceId) ? null : $invoiceId;
    }

    // ── Create Invoice ─────────────────────────────────────────────────────

    public function openInvoiceModal(): void
    {
        $this->resetInvoiceModal();
        $this->showInvoiceModal = true;
    }

    public function createInvoice(): void
    {
        $this->validate([
            'supplierId'    => 'required|exists:suppliers,id',
            'invoiceNumber' => 'required|string|max:100|unique:supplier_invoices,invoice_number',
            'invoiceDate'   => 'required|date',
            'totalAmount'   => 'required|numeric|min:0.01',
            'invoiceNotes'  => 'nullable|string|max:1000',
        ]);

        $total = (float) $this->totalAmount;

        SupplierInvoice::create([
            'supplier_id'    => $this->supplierId,
            'invoice_number' => $this->invoiceNumber,
            'invoice_date'   => $this->invoiceDate,
            'total_amount'   => $total,
            'paid_amount'    => 0,
            'due_amount'     => $total,
            'status'         => 'pending',
            'notes'          => $this->invoiceNotes ?: null,
        ]);

        session()->flash('success', 'Supplier invoice created successfully.');
        $this->resetInvoiceModal();
        $this->showInvoiceModal = false;
    }

    private function resetInvoiceModal(): void
    {
        $this->supplierId          = '';
        $this->invoiceNumber       = '';
        $this->invoiceDate         = now()->format('Y-m-d');
        $this->totalAmount         = '';
        $this->invoiceNotes        = '';
        $this->showNewSupplierForm = false;
        $this->newSupplierName     = '';
        $this->newSupplierPhone    = '';
        $this->resetErrorBag();
    }

    // ── Quick-create Supplier ──────────────────────────────────────────────

    public function createSupplier(): void
    {
        $this->validate([
            'newSupplierName' => 'required|string|max:255',
            'newSupplierPhone' => 'nullable|string|max:30',
        ]);

        $supplier = Supplier::create([
            'name'    => $this->newSupplierName,
            'phone'   => $this->newSupplierPhone ?: null,
            'is_active' => true,
        ]);

        // Auto-select the newly created supplier
        $this->supplierId          = (string) $supplier->id;
        $this->newSupplierName     = '';
        $this->newSupplierPhone    = '';
        $this->showNewSupplierForm = false;

        session()->flash('supplierCreated', 'Supplier "' . $supplier->name . '" added.');
    }

    // ── Pay Modal ──────────────────────────────────────────────────────────

    public function openPayModal(int $invoiceId): void
    {
        $invoice = SupplierInvoice::findOrFail($invoiceId);

        $this->selectedInvoiceId = $invoiceId;
        $this->payAmount         = number_format((float) $invoice->due_amount, 2, '.', '');
        $this->payMethod         = 'cash';
        $this->payReference      = '';
        $this->payDate           = now()->format('Y-m-d');
        $this->payNotes          = '';
        $this->showPayModal      = true;
        $this->resetErrorBag();
    }

    public function recordPayment(): void
    {
        $this->validate([
            'selectedInvoiceId' => 'required|exists:supplier_invoices,id',
            'payAmount'         => 'required|numeric|min:0.01',
            'payMethod'         => 'required|in:cash,bank_transfer,cheque,mobile_money',
            'payReference'      => 'nullable|string|max:255',
            'payDate'           => 'required|date',
            'payNotes'          => 'nullable|string|max:1000',
        ]);

        $invoice = SupplierInvoice::findOrFail($this->selectedInvoiceId);

        SupplierPaymentRecord::create([
            'supplier_invoice_id' => $invoice->id,
            'amount'              => $this->payAmount,
            'payment_method'      => $this->payMethod,
            'reference'           => $this->payReference ?: null,
            'paid_at'             => $this->payDate,
            'notes'               => $this->payNotes ?: null,
        ]);

        // Recalculate totals and status on the invoice
        $invoice->recalculate();

        // Refresh expanded row if this invoice was open
        if ($this->expandedInvoiceId === $invoice->id) {
            $this->expandedInvoiceId = $invoice->id;
        }

        session()->flash('success', 'Payment recorded successfully.');
        $this->showPayModal      = false;
        $this->selectedInvoiceId = null;
    }

    // ── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
        $invoices = SupplierInvoice::with('supplier')
            ->when($this->search, function ($q) {
                $term = "%{$this->search}%";
                $q->where('invoice_number', 'like', $term)
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', $term));
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate(20);

        // Expanded row payment history
        $expandedPayments = null;
        if ($this->expandedInvoiceId) {
            $expandedPayments = SupplierPaymentRecord::where('supplier_invoice_id', $this->expandedInvoiceId)
                ->orderBy('paid_at', 'desc')
                ->get();
        }

        // Stats — computed over the filtered search (global totals without pagination)
        $statsQuery = SupplierInvoice::query()
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus));

        $stats = [
            'total_invoiced' => (float) $statsQuery->sum('total_amount'),
            'total_paid'     => (float) $statsQuery->sum('paid_amount'),
            'total_due'      => (float) $statsQuery->sum('due_amount'),
        ];

        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        return view('livewire.admin.supplier-payments', compact(
            'invoices',
            'expandedPayments',
            'stats',
            'suppliers',
        ));
    }
}
