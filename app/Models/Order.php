<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'tax',
        'shipping_cost',
        'discount',
        'total',
        'shipping_address',
        'payment_method',
        'payment_status',
        'coupon_code',
        'notes',
        // New fields added by 2024_01_07_000001 migration
        'source',
        'advance_percentage',
        'advance_amount',
        'balance_amount',
        'supplier_status',
        'refund_option',
        'shipment_batch_id',
        'waybill_number',
        'delivery_agent',
        'delivery_notes',
        'payment_proof',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address'   => 'array',
            'subtotal'           => 'decimal:2',
            'tax'                => 'decimal:2',
            'shipping_cost'      => 'decimal:2',
            'discount'           => 'decimal:2',
            'total'              => 'decimal:2',
            'advance_amount'     => 'decimal:2',
            'balance_amount'     => 'decimal:2',
        ];
    }

    // ── Core Relationships ────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── New Relationships ─────────────────────────────────────────────

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('created_at', 'asc');
    }

    public function whatsappToken(): HasOne
    {
        return $this->hasOne(WhatsappOrderToken::class);
    }

    public function shipmentBatch(): BelongsTo
    {
        return $this->belongsTo(ShipmentBatch::class, 'shipment_batch_id');
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }

    public function orderReturn(): HasOne
    {
        return $this->hasOne(OrderReturn::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ── Convenience Methods ───────────────────────────────────────────

    /**
     * Check whether this order came in via WhatsApp link.
     */
    public function isWhatsapp(): bool
    {
        return $this->source === 'whatsapp';
    }

    /**
     * Return the confirmed advance payment record, if one exists.
     */
    public function advancePayment(): ?OrderPayment
    {
        return $this->payments()
                    ->where('type', 'advance')
                    ->where('status', 'confirmed')
                    ->first();
    }

    /**
     * Calculate the outstanding balance owed by the customer.
     * balance_amount minus all confirmed balance payments already received.
     */
    public function balanceDue(): float
    {
        $paidBalance = $this->payments()
                            ->where('type', 'balance')
                            ->where('status', 'confirmed')
                            ->sum('amount');

        return max(0, (float) $this->balance_amount - (float) $paidBalance);
    }

    /**
     * Log a status change on this order.
     *
     * @param  string   $toStatus   The new status value
     * @param  string   $notes      Optional admin note
     * @param  int|null $userId     The admin/staff user ID making the change
     */
    public function logStatus(string $toStatus, string $notes = '', ?int $userId = null): OrderStatusLog
    {
        return $this->statusLogs()->create([
            'from_status' => $this->status,
            'to_status'   => $toStatus,
            'notes'       => $notes ?: null,
            'created_by'  => $userId,
        ]);
    }

    // ── Status Display Helpers ────────────────────────────────────────

    /**
     * Return a Tailwind CSS badge class name for the current order status.
     */
    public function statusColor(): string
    {
        return match ($this->status) {
            'new'              => 'badge-secondary',
            'payment_received' => 'badge-warning',
            'confirmed'        => 'badge-info',
            'sourcing'         => 'badge-orange',
            'dispatched'       => 'badge-indigo',
            'delivered'        => 'badge-teal',
            'completed'        => 'badge-success',
            'refunded'         => 'badge-danger',
            'cancelled'        => 'badge-danger',
            // Legacy statuses (should not occur after migration, but kept for safety)
            'pending'          => 'badge-warning',
            'processing'       => 'badge-info',
            'shipped'          => 'badge-indigo',
            default            => 'badge-secondary',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'new'              => 'New',
            'payment_received' => 'Payment Received',
            'confirmed'        => 'Confirmed',
            'sourcing'         => 'Sourcing',
            'dispatched'       => 'Dispatched',
            'delivered'        => 'Delivered',
            'completed'        => 'Completed',
            'refunded'         => 'Refunded',
            'cancelled'        => 'Cancelled',
            default            => ucfirst($this->status),
        };
    }

    /**
     * Return supplier status badge color.
     */
    public function supplierStatusColor(): string
    {
        return match ($this->supplier_status) {
            'none'        => 'badge-secondary',
            'ordered'     => 'badge-orange',
            'received'    => 'badge-success',
            'unavailable' => 'badge-danger',
            default       => 'badge-secondary',
        };
    }

    // ── Static Helpers ────────────────────────────────────────────────

    /**
     * Generate a unique order number in the format MH-XXXXXXXXXX.
     */
    public static function generateOrderNumber(): string
    {
        return 'MH-' . strtoupper(uniqid());
    }
}
