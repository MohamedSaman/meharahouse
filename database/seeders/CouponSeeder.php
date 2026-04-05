<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code'        => 'WELCOME10',
                'type'        => 'percent',
                'value'       => 10,
                'min_order'   => 200,
                'usage_limit' => 100,
                'expires_at'  => now()->addYear(),
                'is_active'   => true,
            ],
            [
                'code'        => 'SAVE50',
                'type'        => 'fixed',
                'value'       => 50,
                'min_order'   => 500,
                'usage_limit' => 50,
                'expires_at'  => now()->addMonths(6),
                'is_active'   => true,
            ],
            [
                'code'        => 'MEHAR20',
                'type'        => 'percent',
                'value'       => 20,
                'min_order'   => 1000,
                'usage_limit' => 30,
                'expires_at'  => now()->addMonths(3),
                'is_active'   => true,
            ],
            [
                'code'        => 'FREESHIP',
                'type'        => 'fixed',
                'value'       => 50,
                'min_order'   => 0,
                'usage_limit' => null,
                'expires_at'  => null,
                'is_active'   => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(['code' => $coupon['code']], $coupon);
        }
    }
}
