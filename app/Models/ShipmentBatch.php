<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShipmentBatch extends Model
{
    protected $fillable = [
        'batch_number', 'name', 'status', 'courier_name', 'tracking_number',
        'courier_cost', 'expected_arrival', 'shipped_at', 'arrived_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'shipped_at'       => 'datetime',
            'arrived_at'       => 'datetime',
            'expected_arrival' => 'date',
            'courier_cost'     => 'decimal:2',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shipment_batch_id');
    }

    public function backorders(): HasMany
    {
        return $this->hasMany(OrderBackorder::class, 'shipment_batch_id');
    }

    public static function generateBatchNumber(): string
    {
        $month = date('Ym');
        $count = self::whereRaw("batch_number LIKE 'SB-{$month}-%'")->count() + 1;
        return "SB-{$month}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'collecting'   => 'Collecting Orders',
            'packed'       => 'Packed in Dubai',
            'shipped'      => 'Shipped from Dubai',
            'in_transit'   => 'In Transit',
            'arrived'      => 'Arrived in Sri Lanka',
            'distributing' => 'Distributing Locally',
            'completed'    => 'Completed',
            default        => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'collecting'   => 'bg-slate-100 text-slate-600',
            'packed'       => 'bg-orange-100 text-orange-700',
            'shipped'      => 'bg-blue-100 text-blue-700',
            'in_transit'   => 'bg-indigo-100 text-indigo-700',
            'arrived'      => 'bg-teal-100 text-teal-700',
            'distributing' => 'bg-purple-100 text-purple-700',
            'completed'    => 'bg-green-100 text-green-700',
            default        => 'bg-slate-100 text-slate-600',
        };
    }
}
