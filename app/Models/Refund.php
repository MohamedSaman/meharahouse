<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'amount',
        'method',
        'customer_bank_account',
        'reference_number',
        'proof_file',
        'notes',
        'status',
        'processed_by',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /**
     * Human-readable method label.
     */
    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'bank_transfer' => 'Bank Transfer',
            'online'        => 'Online',
            'cash'          => 'Cash',
            default         => ucfirst(str_replace('_', ' ', $this->method)),
        };
    }

    /**
     * True if proof_file is an image (jpg / jpeg / png).
     */
    public function isProofImage(): bool
    {
        if (!$this->proof_file) return false;
        $ext = strtolower(pathinfo($this->proof_file, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }
}
