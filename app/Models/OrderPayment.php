<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OrderPayment extends Model
{
    protected $fillable = [
        'order_id',
        'type',
        'amount',
        'method',
        'receipt_path',
        'reference',
        'status',
        'confirmed_by',
        'confirmed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    // Relationships

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeAdvance($query)
    {
        return $query->where('type', 'advance');
    }

    public function scopeBalance($query)
    {
        return $query->where('type', 'balance');
    }

    // Helpers

    /**
     * Return the public URL for the uploaded receipt image.
     * Returns null if no receipt has been uploaded.
     */
    public function receiptUrl(): ?string
    {
        if (!$this->receipt_path) {
            return null;
        }

        return asset('storage/' . $this->receipt_path);
    }

    /**
     * Check whether this payment has a receipt awaiting admin review.
     */
    public function hasPendingReceipt(): bool
    {
        return $this->status === 'pending' && !empty($this->receipt_path);
    }
}
