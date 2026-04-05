<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products  = Product::active()->get();
        $statuses  = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $payments  = ['pending', 'paid', 'paid', 'paid', 'failed'];
        $methods   = ['cash_on_delivery', 'bank_transfer', 'mobile_money'];

        if ($customers->isEmpty() || $products->isEmpty()) return;

        for ($i = 0; $i < 20; $i++) {
            $customer    = $customers->random();
            $statusIndex = array_rand($statuses);
            $status      = $statuses[$statusIndex];
            $payStatus   = $status === 'cancelled' ? 'failed' : $payments[$statusIndex];

            $orderProducts = $products->random(rand(1, 4));
            $subtotal      = 0;
            $items         = [];

            foreach ($orderProducts as $product) {
                $qty      = rand(1, 3);
                $price    = (float) ($product->sale_price ?? $product->price);
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;
                $items[]   = [
                    'product'      => $product,
                    'qty'          => $qty,
                    'price'        => $price,
                    'subtotal'     => $lineTotal,
                ];
            }

            $tax      = round($subtotal * 0.15, 2);
            $shipping = $subtotal >= 500 ? 0 : 50;
            $total    = round($subtotal + $tax + $shipping, 2);

            $address = [
                'full_name'  => $customer->name,
                'email'      => $customer->email,
                'phone'      => $customer->phone ?? '+251 911 000 000',
                'address'    => 'Sample Street ' . rand(1, 100),
                'city'       => ['Addis Ababa', 'Dire Dawa', 'Hawassa', 'Bahir Dar', 'Mekelle'][rand(0, 4)],
                'region'     => 'Ethiopia',
                'postal_code' => '',
            ];

            $order = Order::create([
                'user_id'          => $customer->id,
                'order_number'     => Order::generateOrderNumber(),
                'status'           => $status,
                'subtotal'         => $subtotal,
                'tax'              => $tax,
                'shipping_cost'    => $shipping,
                'discount'         => 0,
                'total'            => $total,
                'shipping_address' => $address,
                'payment_method'   => $methods[array_rand($methods)],
                'payment_status'   => $payStatus,
                'created_at'       => now()->subDays(rand(0, 90)),
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'price'        => $item['price'],
                    'quantity'     => $item['qty'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }
        }
    }
}
