<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'subtotal',
        'status',
        'refund_amount',
        'original_qty',
        'original_ordered_subtotal',
        'is_replaced',
        'original_product_id',
        'original_product_name',
        'original_price',
        'original_subtotal',
        'replacement_notes',
        'replaced_at',
        'replaced_by',
    ];

    protected function casts(): array
    {
        return [
            'price'                     => 'decimal:2',
            'subtotal'                  => 'decimal:2',
            'refund_amount'             => 'decimal:2',
            'original_ordered_subtotal' => 'decimal:2',
            'is_replaced'               => 'boolean',
            'original_price'            => 'decimal:2',
            'original_subtotal'         => 'decimal:2',
            'replaced_at'               => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function originalProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'original_product_id');
    }
}
