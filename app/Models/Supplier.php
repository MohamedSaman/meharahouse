<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'contact_person', 'email', 'phone', 'whatsapp',
        'address', 'city', 'country', 'website', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function totalOrders(): int
    {
        return $this->purchaseOrders()->count();
    }

    public function totalSpent(): float
    {
        return (float) $this->purchaseOrders()
            ->whereIn('status', ['received', 'partial'])
            ->sum('total');
    }
}
