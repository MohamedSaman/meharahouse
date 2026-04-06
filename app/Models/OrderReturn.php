<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'condition',
        'pickup_address',
        'pickup_date',
        'reason',
        'notes',
        'created_by',
        'received_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'received_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Display Helpers ───────────────────────────────────────────────

    public function statusLabel(): string
    {
        return match ($this->status) {
            'requested'       => 'Return Requested',
            'pickup_arranged' => 'Pickup Arranged',
            'received'        => 'Item Received',
            'resold'          => 'Resold (Ready Stock)',
            'sent_back_dubai' => 'Sent Back to Dubai',
            'closed'          => 'Closed',
            default           => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'requested'       => 'bg-amber-100 text-amber-700',
            'pickup_arranged' => 'bg-blue-100 text-blue-700',
            'received'        => 'bg-purple-100 text-purple-700',
            'resold'          => 'bg-emerald-100 text-emerald-700',
            'sent_back_dubai' => 'bg-slate-100 text-slate-600',
            'closed'          => 'bg-slate-100 text-slate-500',
            default           => 'bg-slate-100 text-slate-600',
        };
    }
}
