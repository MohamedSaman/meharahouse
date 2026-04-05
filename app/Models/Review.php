<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'product_id',
        'rating',
        'title',
        'description',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'rating'      => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'approved' => 'emerald',
            'rejected' => 'red',
            default    => 'amber',
        };
    }

    public function starLabel(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}
