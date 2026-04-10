<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderBackorder extends Model
{
    protected $fillable = [
        'backorder_number',
        'order_id',
        'shipment_batch_id',
        'order_item_id',
        'product_id',
        'replacement_product_id',
        'replacement_price',
        'replacement_notes',
        'product_name',
        'ordered_qty',
        'available_qty',
        'short_qty',
        'decision',
        'status',
        'notes',
        'created_by',
        'dispatched_by',
        'fulfilled_at',
        'dispatched_at',
        'delivered_at',
    ];

    protected $casts = [
        'fulfilled_at'     => 'datetime',
        'dispatched_at'    => 'datetime',
        'delivered_at'     => 'datetime',
        'replacement_price'=> 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $bo) {
            if (!$bo->backorder_number) {
                $bo->backorder_number = static::generateBoNumber();
            }
        });
    }

    public static function generateBoNumber(): string
    {
        $prefix = 'BO-' . now()->format('ymd') . '-';
        $last   = static::where('backorder_number', 'like', $prefix . '%')
            ->orderByDesc('id')->value('backorder_number');
        $seq = $last ? ((int) substr($last, -3) + 1) : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function shipmentBatch(): BelongsTo
    {
        return $this->belongsTo(ShipmentBatch::class);
    }

    public function replacementProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'replacement_product_id');
    }

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function decisionLabel(): string
    {
        return match ($this->decision) {
            'repurchase' => 'Repurchase',
            'waitlist'   => 'Waitlist',
            'replace'    => 'Replace',
            default      => 'Pending',
        };
    }

    public function isReplacement(): bool
    {
        return $this->decision === 'replace' && $this->replacement_product_id !== null;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'      => 'Pending',
            'repurchasing' => 'Repurchasing',
            'ready'        => 'Ready to Dispatch',
            'dispatched'   => 'Dispatched',
            'delivered'    => 'Delivered',
            'completed'    => 'Completed',
            'cancelled'    => 'Cancelled',
            default        => ucfirst($this->status),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending'      => 'bg-amber-100 text-amber-700 border-amber-200',
            'repurchasing' => 'bg-blue-100 text-blue-700 border-blue-200',
            'ready'        => 'bg-violet-100 text-violet-700 border-violet-200',
            'dispatched'   => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            'delivered'    => 'bg-teal-100 text-teal-700 border-teal-200',
            'completed'    => 'bg-green-100 text-green-700 border-green-200',
            'cancelled'    => 'bg-slate-100 text-slate-500 border-slate-200',
            default        => 'bg-slate-100 text-slate-500 border-slate-200',
        };
    }

    public function isActive(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }
}
