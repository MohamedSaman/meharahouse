<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPaymentRecord extends Model
{
    protected $fillable = [
        'customer_account_id',
        'amount',
        'payment_type',
        'payment_method',
        'reference',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'  => 'decimal:2',
            'paid_at' => 'date',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(CustomerAccount::class, 'customer_account_id');
    }

    public function typeLabel(): string
    {
        return match ($this->payment_type) {
            'advance' => 'Advance',
            'payment' => 'Payment',
            default   => ucfirst($this->payment_type),
        };
    }

    public function methodLabel(): string
    {
        return match ($this->payment_method) {
            'cash'          => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'mobile_money'  => 'Mobile Money',
            'telebirr'      => 'Telebirr',
            'cbebirr'       => 'CBE Birr',
            default         => ucfirst($this->payment_method),
        };
    }
}
