<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierInvoice extends Model
{
    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'invoice_date',
        'total_amount',
        'paid_amount',
        'due_amount',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'total_amount' => 'decimal:2',
            'paid_amount'  => 'decimal:2',
            'due_amount'   => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function paymentRecords(): HasMany
    {
        return $this->hasMany(SupplierPaymentRecord::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ── Status Helpers ─────────────────────────────────────────────────────

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'partial' => 'blue',
            'paid'    => 'green',
            default   => 'gray',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'partial' => 'Partial',
            'paid'    => 'Paid',
            default   => ucfirst($this->status),
        };
    }

    // ── Business Logic ─────────────────────────────────────────────────────

    /**
     * Recalculate paid_amount, due_amount, and status from payment records.
     * Call this after inserting / deleting a SupplierPaymentRecord.
     */
    public function recalculate(): void
    {
        $paid = (float) $this->paymentRecords()->sum('amount');
        $total = (float) $this->total_amount;
        $due  = max(0, $total - $paid);

        $status = 'pending';
        if ($paid >= $total) {
            $status = 'paid';
        } elseif ($paid > 0) {
            $status = 'partial';
        }

        $this->update([
            'paid_amount' => $paid,
            'due_amount'  => $due,
            'status'      => $status,
        ]);
    }
}
