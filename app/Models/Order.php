<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'subtotal'         => 'decimal:2',
            'tax'              => 'decimal:2',
            'shipping_cost'    => 'decimal:2',
            'discount'         => 'decimal:2',
            'total'            => 'decimal:2',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // Status badge helper
    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'    => 'yellow',
            'processing' => 'blue',
            'shipped'    => 'purple',
            'delivered'  => 'green',
            'cancelled'  => 'red',
            default      => 'gray',
        };
    }

    public function statusLabel(): string
    {
        return ucfirst($this->status);
    }

    // Generate unique order number
    public static function generateOrderNumber(): string
    {
        return 'MH-' . strtoupper(uniqid());
    }
}
