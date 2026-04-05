<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_id', 'po_number', 'status', 'subtotal',
        'shipping_cost', 'total', 'currency', 'notes',
        'expected_date', 'ordered_at', 'received_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_date' => 'date',
            'ordered_at'    => 'datetime',
            'received_at'   => 'datetime',
            'subtotal'      => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total'         => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public static function generatePoNumber(): string
    {
        return 'PO-' . strtoupper(Str::random(6)) . '-' . date('Ymd');
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'draft'     => 'slate',
            'ordered'   => 'blue',
            'partial'   => 'amber',
            'received'  => 'green',
            'cancelled' => 'red',
            default     => 'slate',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'     => 'Draft',
            'ordered'   => 'Ordered',
            'partial'   => 'Partially Received',
            'received'  => 'Fully Received',
            'cancelled' => 'Cancelled',
            default     => ucfirst($this->status),
        };
    }
}
