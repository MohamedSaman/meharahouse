<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerAccount extends Model
{
    protected $fillable = [
        'order_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'total_amount',
        'paid_amount',
        'due_amount',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_amount'  => 'decimal:2',
            'due_amount'   => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentRecords(): HasMany
    {
        return $this->hasMany(CustomerPaymentRecord::class);
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
     * Call this after inserting / deleting a CustomerPaymentRecord.
     */
    public function recalculate(): void
    {
        $paid  = (float) $this->paymentRecords()->sum('amount');
        $total = (float) $this->total_amount;
        $due   = max(0, $total - $paid);

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
