<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappOrderToken extends Model
{
    protected $fillable = [
        'token',
        'created_by',
        'products',
        'subtotal',
        'advance_percentage',
        'advance_amount',
        'expires_at',
        'used_at',
        'order_id',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'products'       => 'array',
            'subtotal'       => 'decimal:2',
            'advance_amount' => 'decimal:2',
            'used_at'        => 'datetime',
            'expires_at'     => 'datetime',
        ];
    }

    // Relationships

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Business Logic

    /**
     * Determine whether this token can still be used by a customer.
     * A token is usable only if it has status 'pending' and has not been used yet.
     */
    public function isUsable(): bool
    {
        if ($this->status !== 'pending' || $this->used_at !== null) {
            return false;
        }

        // Check expiry if set
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Mark this token as used once the customer submits the order form.
     */
    public function markUsed(int $orderId): void
    {
        $this->update([
            'status'   => 'used',
            'used_at'  => now(),
            'order_id' => $orderId,
        ]);
    }

    /**
     * Create a new WhatsApp order token.
     *
     * @param  int    $adminId        The admin user ID generating the link
     * @param  array  $products       Array of {product_id, product_name, quantity, price}
     * @param  float  $subtotal       Total cart value
     * @param  int    $advancePct     Advance percentage (from settings, e.g. 50)
     * @param  float  $advanceAmt     Pre-calculated advance amount
     * @param  string $notes          Optional admin note about this customer/order
     */
    public static function generate(
        int $adminId,
        array $products,
        float $subtotal,
        int $advancePct,
        float $advanceAmt,
        string $notes = ''
    ): self {
        return static::create([
            'token'              => bin2hex(random_bytes(32)), // 64-char hex string
            'created_by'        => $adminId,
            'products'          => $products,
            'subtotal'          => $subtotal,
            'advance_percentage' => $advancePct,
            'advance_amount'    => $advanceAmt,
            'status'            => 'pending',
            'notes'             => $notes,
        ]);
    }
}
